import { dbVersion } from "./data/db.js";

const MAX_ATTEMPTS = 20;
const TEMP_PLAN_ID = "$TMP";

let Disciplines = {};
let Exercises = {};
let Auslaufen = {};
class TrainingPlan {

  static messages = [];
  static favorites = [];

  static version = {}

  constructor(id,disciplineIds,createdBy="",createdAt="",description="") {
    this.id = id;
    this.created_by = createdBy;
    this.created_at = createdAt;
    this.description = description;
    this.disciplines = disciplineIds.map( (id) => Disciplines.find( (discipline) => discipline.id==id ) );
    this.warmup = []; 
    this.mainex = [];
    this.ending = [];
    this.suitable = { warmup: [], mainex: [], ending: [], runabc: [] };
  }

  static async loadData() {
    // Determine the version of the database, including the data loader function implementations
    this.version = await dbVersion();
    console.log("Version info: " + JSON.stringify(this.version));

    // Load the Disciplines from the CSV file or the database
    Disciplines = await this.version.disciplineLoader();
    // Load the Exercises from the CSV or the database
    Exercises = await this.version.exerciseLoader();
    Exercises.sort( (a,b) => a.name.localeCompare(b.name) );
    // Make the "Auslaufen" exercise "sticky" so that it always remains the last
    Auslaufen = Exercises.find( (exercise) => exercise.id==='Auslaufen');
    Auslaufen.duration = 5;
    Auslaufen.sticky = true;

    // Load the favorites from the CSV file or the database
    this.favorites = [];
    let rawFavoriteData = await this.version.favoritesLoader();
    rawFavoriteData.headers.forEach( (favorite) => {
      let plan = new TrainingPlan(favorite.id, favorite.disciplines, favorite.created_by, favorite.created_at, favorite.description);
      rawFavoriteData.exerciseMap.filter( (mapItem) => mapItem.favorite_id==favorite.id )
                                 .forEach((mapItem) => {
         // copy the exercise from the Exercises array to be able to set the duration
         let exercise = Object.assign({},Exercises.find( (ex) => ex.id==mapItem.exercise_id ));
         exercise.duration = parseInt(mapItem.duration);
         plan[mapItem.phase][mapItem.position-1]= exercise;
         // TODO: also populate the suitable arrays?
      });
      this.favorites.push(plan);
    });
  }

  static getAllDisciplines() {
    return Disciplines;
  }

  static getAvailableFavorites(forDisciplineIds, targetDuration) {
    let availableFavorites = this.favorites.filter( (plan) => 
         forDisciplineIds.map( (id) => Disciplines.find( (discipline) => discipline.id==id ) )
                         .every( (selected) => plan.disciplines.includes(selected) )
      && plan.duration()==targetDuration);
    return availableFavorites; 
  }

  static generate(forDisciplineIds, targetDuration) {
    this.messages = [];
    if (forDisciplineIds.length==0) {
      this.messages.push("Bitte wähle mindestens eine Disziplin aus.");
      return null;
    }

    // find the exercises that are suitable for the selected disciplines
    // and reset their duration to the minimum duration
    let suitableExercises = Exercises.filter(
      (exercise) =>  forDisciplineIds.some( (selected) => exercise.disciplines.includes(selected) ));
    suitableExercises.forEach( (exercise) => exercise.duration = exercise.durationmin);
    
    let plan = new TrainingPlan(TEMP_PLAN_ID,forDisciplineIds);

    const phases = [["warmup", "Aufwärm"], ["runabc", "Lauf-ABC"], ["mainex", "Haupt"], ["ending", "Schluss"]];
    for(let i=0; i<phases.length; i++) {
      let phase = phases[i][0];
      plan.suitable[phase] = suitableExercises.filter( (exercise) => exercise[phase] );
      // console.log(`${phases[i][1]}übungen: ${JSON.stringify(plan.suitable[phase])}`);
      if(plan.suitable[phase].length==0) {
        TrainingPlan.messages.push(`Es gibt keine ${phases[i][1]}übungen für die ausgewählten Disziplinen.`);
        return null;
      }
    }
    
    // the following algorithm is based purely on randomly picking exercises and does
    // not consider potential dependencies between exercises
    let attempts = 0;
    while(attempts++<MAX_ATTEMPTS) {
      // pick a random warmup and a random runabc
      plan.warmup = [ plan.suitable.warmup.at(Math.floor(Math.random()*plan.suitable.warmup.length)),
                      plan.suitable.runabc.at(Math.floor(Math.random()*plan.suitable.runabc.length)) ];
      // pick a random ending and add the standard Auslaufen
      plan.ending = [ plan.suitable.ending.at(Math.floor(Math.random()*plan.suitable.ending.length)),
                      Auslaufen ];
      // pick main exercises until the target duration is reached or exceeded.
      plan.mainex = [];
      let minDuration = 0;
      let maxDuration = 0;
      do {
        let index = Math.floor(Math.random()*plan.suitable.mainex.length);
        let exerciseToAdd = plan.suitable.mainex.at(index);
        if(!plan.mainex.includes(exerciseToAdd)) {
          plan.mainex.push(exerciseToAdd);
          [minDuration, maxDuration] = plan.durationRange();
        }
      } while(maxDuration<=targetDuration);
      // check if the plan is good enough: 3-4 main exercises and still within the target duration
      if(plan.mainex.length>=3 && plan.mainex.length<=4 && minDuration<=targetDuration) {
        // set the duration of those exercises for which there is still a range, starting with the main exercises
        let variableExercises = plan.mainex
          .concat(plan.warmup)
          .concat(plan.ending)
          .filter( (exercise) => exercise.durationmin!=exercise.durationmax );
        for(let i=0; i<variableExercises.length && plan.duration()<targetDuration; i++) {
          variableExercises[i].duration = variableExercises[i].durationmax;
        }
        if(plan.duration()==targetDuration) break;
      }
    }
    if(attempts>=MAX_ATTEMPTS) {
      TrainingPlan.messages.push(`Ich konnte nach ${MAX_ATTEMPTS} Versuchen keinen Plan finden, der deinen Anforderungen entspricht.`);
      TrainingPlan.messages.push("Bitte versuche es erneut. Manchmal hilft es, eine weitere Disziplin auszuwählen.");
      return undefined;
    }
    return plan;
  }

  duration() {
    return this.mainex.reduce( durationAdder, this.ending.reduce( durationAdder, this.warmup.reduce( durationAdder, 0 ) ) );;
  }

  durationRange() {
    const minDuration = this.mainex.reduce( minAdder, this.ending.reduce( minAdder, this.warmup.reduce( minAdder, 0 ) ) );  
    const maxDuration = this.mainex.reduce( maxAdder, this.ending.reduce( maxAdder, this.warmup.reduce( maxAdder, 0 ) ) );
    return [minDuration, maxDuration];
  }

  moveExerciseUp(exerciseId) {
    let index = this.mainex.findIndex( (exercise) => exercise.id==exerciseId );
    if(index>0) {
      let predecessor = this.mainex[index-1];
      this.mainex[index-1] = this.mainex[index];
      this.mainex[index] = predecessor;
    }
  }

  moveExerciseDown(exerciseId) {
    let index = this.mainex.findIndex( (exercise) => exercise.id==exerciseId );
    if(index<this.mainex.length-1) {
      let successor = this.mainex[index+1];
      this.mainex[index+1] = this.mainex[index];
      this.mainex[index] = successor;
    }
  }

  replaceExercise(phase, exerciseId) {
    let index = this[phase].findIndex( (exercise) => exercise.id==exerciseId );
    let newExercise = undefined;
    // Special treatment of runabc in the warmup phase
    let searchedPhase = (phase==="warmup" && index==1) ? "runabc" : phase;
    do {
      newExercise = this.suitable[searchedPhase].at(Math.floor(Math.random()*this.suitable[searchedPhase].length));
      if(newExercise.duration<this[phase][index].duration && newExercise.durationmax>=this[phase][index].duration) {
        newExercise.duration = this[phase][index].duration;
      }
    } while( (this[phase].includes(newExercise)) || (newExercise.duration!=this[phase][index].duration));  
    this[phase][index] = newExercise;
  }
};

function adder(field, total, exercise) { return total + exercise[field] };
const minAdder = adder.bind(null, "durationmin");
const maxAdder = adder.bind(null, "durationmax");
const durationAdder = adder.bind(null, "duration");

export { TrainingPlan, Exercises };
import { dbVersion } from "./db.js";

const MAX_ATTEMPTS = 20;
const TEMP_PLAN_ID = "$TMP";

const App = {
  version: undefined,

  async getVersion(pathPrefix="./") {
    if( !this.version) {
      this.version = await dbVersion(pathPrefix);
      Discipline.loader = this.version.disciplineLoader;
      Exercise.loader = this.version.exerciseLoader;
      Exercise.save = this.version.exerciseSaver;
      TrainingPlan.loader = this.version.favoritesLoader;
      TrainingPlan.save = this.version.favoritesSaver;
      console.log("App version info: " + JSON.stringify(this.version));
    }
    return this.version;
  },
}

const Discipline = {
  Instances: [],
  async loadAll() {
    return this.loader()
      .then( rawData => { this.Instances = rawData;});
  },
  getAll() {
    this.Instances.sort( (a,b) => a.name.localeCompare(b.name) );
    return this.Instances; 
  }
}

class Exercise {

  static async loadAll() {
    return this.loader()
      .then( rawData => this.createInstances(rawData) );
  }

  constructor(id, name, disciplines, durationmin=0, durationmax=0, warmup=false, 
      runabc=false, mainex=false, ending=false, repeats='', material='', details='') {
    this.id = id;
    this.name = name;
    this.disciplines = disciplines;
    this.durationmin = durationmin;
    this.durationmax = durationmax;
    this.repeats = repeats;
    this.material = material;
    this.details = Array.isArray(details) ? ( (details.length>0 && details[0].length>0) ? details : '' ) : details;
    this.warmup = warmup;
    this.runabc = runabc;
    this.mainex = mainex;
    this.ending = ending;
    this.duration = durationmin; // default to minimum duration
  }

  static Auslaufen = undefined;
  static Instances = [];

  static createInstances(rawData) {
    this.Instances = [];
    rawData.forEach( (exercise) => {
      let newExercise = new Exercise(exercise.id, exercise.name, exercise.disciplines, exercise.durationmin, exercise.durationmax,
        exercise.warmup, exercise.runabc, exercise.mainex, exercise.ending, exercise.repeats, exercise.material, exercise.details);
      if(newExercise.id==='Auslaufen') {
        newExercise.duration = 5; // Auslaufen always has a fixed duration of 5 minutes
        newExercise.sticky = true; // Auslaufen is a "sticky" exercise, always at the end of the plan
        Exercise.Auslaufen = newExercise;
      }
      this.Instances.push(newExercise);
    });
  }

  static getAll() {
    this.Instances.sort( (a,b) => a.name.localeCompare(b.name) );
    return this.Instances;
  }

  copy() {
    let theCopy = Object.create(this);
    // generate a new id based on the id of the copy source
    let idParts = theCopy.id.split(/_\d+$/);
    let sameStartIds = Exercise.Instances.filter( ex => ex.id.startsWith(idParts[0]));
    if( sameStartIds.length === 1) { // at least the selected exercise's Id should be in the array
      theCopy.id += '_01';
    } else { // there are more ids starting with the same sequence -> get the one with highest number
      sameStartIds.sort( (a,b) => a.id.localeCompare(b.id) ); // sort by id
      let lastSameStartIdParts = sameStartIds.pop().id.split(/_(\d+)$/);
      let newNum = parseInt(lastSameStartIdParts[1],10) + 1;
      theCopy.id = idParts[0] + '_' + ( isNaN(newNum) ? '01' : (newNum < 10 ? '0' + newNum : newNum ));
    }
    theCopy.name += " (Kopie)";
    return theCopy;    
  }

  equals(that) {
    let thisDetails = Array.isArray(this.details) ? this.details.join(':') : this.details;
    let thatDetails = Array.isArray(that.details) ? that.details.join(':') : that.details;
    return this.id === that.id &&
           this.name === that.name &&
           this.disciplines.sort().join(",") === that.disciplines.sort().join(",") &&
           this.durationmin === that.durationmin &&
           this.durationmax === that.durationmax &&
           this.warmup === that.warmup &&
           this.runabc === that.runabc &&
           this.mainex === that.mainex &&
           this.ending === that.ending &&
           this.repeats === that.repeats &&
           this.material === that.material &&
           thisDetails === thatDetails;
  }

  containedInFavoritePlans() {
    return TrainingPlan.Favorites.filter( (plan) => 
      plan.mainex.some( (exercise) => exercise.id==this.id ) ||
      plan.warmup.some( (exercise) => exercise.id==this.id ) ||
      plan.ending.some( (exercise) => exercise.id==this.id )
    );
  }

  isNotInDb() {
    return Exercise.getAll().find(exercise => exercise.id === this.id) ? false : true;
  }

  save() {
    this.details = Array.isArray(this.details) ? this.details.join(':') : this.details;
    return Exercise.save(this.isNotInDb() ? "create" : "update" , this)
      .then( result => Exercise.loadAll() // reload data to ensure it is up-to-date
      .then(() => result) );
  }

  delete() {
    return Exercise.save("delete" , this.id)
      .then( result => Exercise.loadAll() // reload data to ensure it is up-to-date
      .then(() => result) );
  }
}

class TrainingPlan {

  static async loadAll() {
    return this.loader()
      .then( rawData => this.createInstances(rawData) );
  }

  static messages = [];
  static Favorites = [];

  constructor(id,disciplineIds,createdBy="",createdAt="",description="") {
    this.id = id;
    this.created_by = createdBy;
    this.created_at = createdAt;
    this.description = description;
    this.disciplines = disciplineIds.map( (id) => Discipline.getAll().find( (discipline) => discipline.id==id ) );
    this.warmup = []; 
    this.mainex = [];
    this.ending = [];
    this.suitable = { warmup: [], mainex: [], ending: [], runabc: [] };
  }

  static createInstances(rawData) {
    this.Favorites = [];
    rawData.headers.forEach( (favorite) => {
      let plan = new TrainingPlan(favorite.id, favorite.disciplines, favorite.created_by, favorite.created_at, favorite.description);
      rawData.exerciseMap.filter( (mapItem) => mapItem.favorite_id==favorite.id )
                         .forEach((mapItem) => {
         // copy the exercise from the Exercises array to be able to set the duration
         let exercise = Object.assign({},Exercise.getAll().find( (ex) => ex.id==mapItem.exercise_id ));
         exercise.duration = parseInt(mapItem.duration);
         plan[mapItem.phase][mapItem.position-1]= exercise;
      });
      plan.setSuitableExercises(favorite.disciplines);
      plan.modified = false; // plans are not modified when loaded from the database
      this.Favorites.push(plan);
    });
    this.Favorites.sort( (a,b) => parseInt(a.id) < parseInt(b.id) ? -1 : 1 );
  }

  static getAvailableFavorites(forDisciplineIds, targetDuration) {
    const disciplines = Discipline.getAll();
    let availableFavorites = this.Favorites.filter( (plan) => 
      forDisciplineIds.map( (id) => disciplines.find( (discipline) => discipline.id==id ) )
                      .every( (selected) => plan.disciplines.includes(selected) )
      && plan.duration()==targetDuration);
    return availableFavorites; 
  }

  static generate(forDisciplineIds, targetDuration) {
    if (forDisciplineIds.length==0) {
      return { plan: undefined, messages: ["Bitte wähle mindestens eine Disziplin aus."] };
    }
    let plan = new TrainingPlan(TEMP_PLAN_ID, forDisciplineIds);
    let messages = plan.setSuitableExercises(forDisciplineIds);
    if(messages.length>0) {
      return { plan: undefined, messages: messages };
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
                      Exercise.Auslaufen ];
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
      messages.push(`Ich konnte nach ${MAX_ATTEMPTS} Versuchen keinen Plan finden, der deinen Anforderungen entspricht.`);
      messages.push("Bitte versuche es erneut. Manchmal hilft es, eine weitere Disziplin auszuwählen.");
      return { plan: undefined, messages: messages };
    }
    return { plan: plan, messages: messages };
  }

  setSuitableExercises(forDisciplineIds) {
    let messages = [];
    let suitableExercises = Exercise.getAll().filter(
      (exercise) =>  forDisciplineIds.some( (selected) => exercise.disciplines.includes(selected) ));
    suitableExercises.forEach( (exercise) => exercise.duration = exercise.durationmin);
    
    const phases = [["warmup", "Aufwärm"], ["runabc", "Lauf-ABC"], ["mainex", "Haupt"], ["ending", "Schluss"]];
    for(let i=0; i<phases.length; i++) {
      let phase = phases[i][0];
      this.suitable[phase] = suitableExercises.filter( (exercise) => exercise[phase] );
      // console.log(`${phases[i][1]}übungen: ${JSON.stringify(plan.suitable[phase])}`);
      if(this.suitable[phase].length==0) {
        messages.push(`Es gibt keine ${phases[i][1]}übungen für die ausgewählten Disziplinen.`);
      }
    } 
    return messages;
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
      if(this.id !== TEMP_PLAN_ID) this.modified = true;
    }
  }

  moveExerciseDown(exerciseId) {
    let index = this.mainex.findIndex( (exercise) => exercise.id==exerciseId );
    if(index<this.mainex.length-1) {
      let successor = this.mainex[index+1];
      this.mainex[index+1] = this.mainex[index];
      this.mainex[index] = successor;
      if(this.id !== TEMP_PLAN_ID) this.modified = true;
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
    if(this.id !== TEMP_PLAN_ID && newExercise.id !== this[phase][index].id ) this.modified = true;
    this[phase][index] = newExercise;
  }

  isModified() {
    if(this.id===TEMP_PLAN_ID) {
      return true; // temporary plans are always modified}
    } else {
      return this.modified;
    }
  }
  
  save() {
    return TrainingPlan.save("update", this)
      .then( result => TrainingPlan.loadAll() // reload data to ensure it is up-to-date
      .then(() => result) );
  }

  saveAs(description) {
    if(description===undefined || description.trim()==="") {
      return Promise.reject("Die Beschreibung darf nicht leer sein.");
    }
    this.description = description.trim();;
    this.id = parseInt(TrainingPlan.Favorites[TrainingPlan.Favorites.length-1].id) + 1;
    this.created_by = "markus"; // TODO: get the current user
    return TrainingPlan.save("create" , this)
      .then( result => TrainingPlan.loadAll() // reload data to ensure it is up-to-date
      .then(() => result) );
  }

  delete() {
    if(this.id===TEMP_PLAN_ID) {
      // this is a temporary plan, so just return
      return;
    } else {
      return TrainingPlan.save("delete" , this.id)
      .then( result => TrainingPlan.loadAll() // reload data to ensure it is up-to-date
      .then(() => result) );
    }
  }

};

function adder(field, total, exercise) { return total + exercise[field] };
const minAdder = adder.bind(null, "durationmin");
const maxAdder = adder.bind(null, "durationmax");
const durationAdder = adder.bind(null, "duration");

export { App, Discipline, Exercise, TrainingPlan };
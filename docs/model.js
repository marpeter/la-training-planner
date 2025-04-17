import { loadObjectFromCSV } from "./data/utils.js";

let Disciplines = {};
let Exercises = {};
class TrainingPlan {

  constructor(disciplines) {
    this.id = 1;
    this.disciplines = disciplines;
    this.warmup = []; 
    this.mainex = [];
    this.ending = [];
    this.suitable = { warmup: [], mainex: [], ending: [], runabc: [] };
  }

  duration() {
    return this.mainex.reduce( adder, this.ending.reduce( adder, this.warmup.reduce( adder, 0 ) ) );
  }

  static async loadData() {
    Disciplines = await loadObjectFromCSV('data/Disciplines.csv');
    Exercises = await loadObjectFromCSV('data/Exercises.csv',/;/);
    Exercises.Auslaufen = { id: "Auslaufen", name: "Auslaufen", warmup: false, runabc: false, mainex: false, ending: false, sticky: true, material: "", duration: "5", repeat: "2 Runden", disciplines: [], details: []};
  }

  static getAllDisciplines() {
    return Disciplines;
  }

  static generate(forDisciplineIds, targetDuration) {
    if (forDisciplineIds.length==0) return null;

    let suitableExercises = Object.values(Exercises).filter(
      (exercise) => forDisciplineIds.some( (selected) => exercise.disciplines.includes(selected) ));

    // console.log("Suitable: " + JSON.stringify(suitableExercises));

    const forDisciplines = forDisciplineIds.map( (id) => Disciplines[id]);
    let plan = new TrainingPlan(forDisciplines);

    plan.suitable.warmup = suitableExercises.filter( (exercise) => exercise.warmup );
    plan.suitable.runabc = suitableExercises.filter( (exercise) => exercise.runabc );
    plan.suitable.mainex = suitableExercises.filter( (exercise) => exercise.mainex );
    plan.suitable.ending = suitableExercises.filter( (exercise) => exercise.ending ); 
  
    // console.log(warmups.length + " Warm-ups: " + JSON.stringify(plan.suitable.warmup));
    // console.log("RunABCs: " + JSON.stringify(plan.suitable.runabc));
    // console.log("Main exercises: " + JSON.stringify(plan.suitable.mainex));
    // console.log("Endings: " + JSON.stringify(plan.suitable.ending));

    // the following algorithm is based purely on randomly picking exercises and does
    // not consider potential dependencies between exercises
    let attempts = 0;
    while((plan.duration()!=targetDuration) && (attempts++<10)) {
      console.log("Attempt " + attempts);
      // pick a random warmup and a random runabc
      plan.warmup = [ plan.suitable.warmup.at(Math.floor(Math.random()*plan.suitable.warmup.length)),
                      plan.suitable.runabc.at(Math.floor(Math.random()*plan.suitable.runabc.length)) ];
      // pick a random ending and add the standard Auslaufen
      plan.ending = [ plan.suitable.ending.at(Math.floor(Math.random()*plan.suitable.ending.length)),
                      Exercises.Auslaufen ];
      // pick main exercises until the target duration is reached or exceeded.
      plan.mainex = [];
      while(plan.duration()<=targetDuration-10) {
        let index = Math.floor(Math.random()*plan.suitable.mainex.length);
        let exerciseToAdd = plan.suitable.mainex.at(index);
        if(!plan.mainex.includes(exerciseToAdd)) {
          plan.mainex.push(exerciseToAdd);
        }
      }
    }
    if(attempts>=10) return undefined;
    return plan;
  }

  moveExerciseUp(exerciseId) {
    let index = this.mainex.findIndex( (exercise) => exercise.id==exerciseId );
    let newMainex = []; 
    for (let i=0; i<index-1; i++) {
      newMainex.push(this.mainex[i]);
    }
    newMainex.push(this.mainex[index]);
    newMainex.push(this.mainex[index-1]);
    for (let i=index+1; i<this.mainex.length; i++) {
      newMainex.push(this.mainex[i]);
    }
    this.mainex = newMainex;
  }
  moveExerciseDown(exerciseId) {
    let index = this.mainex.findIndex( (exercise) => exercise.id==exerciseId );
    let newMainex = []; 
    for (let i=0; i<index; i++) {
      newMainex.push(this.mainex[i]);
    }
    newMainex.push(this.mainex[index+1]);
    newMainex.push(this.mainex[index]);
    for (let i=index+2; i<this.mainex.length; i++) {
      newMainex.push(this.mainex[i]);
    }
    this.mainex = newMainex;
  }
  replaceExercise(phase, exerciseId) {
    let index = this[phase].findIndex( (exercise) => exercise.id==exerciseId );
    let newExercise = undefined;
    // Special treatment of runabc in the warmup phase
    let searchedPhase = phase;
    if(phase==="warmup" && index==1) {
      searchedPhase = "runabc";
    }
    do {
      newExercise = this.suitable[searchedPhase].at(Math.floor(Math.random()*this.suitable[searchedPhase].length));
    } while( (newExercise.id===exerciseId) || (newExercise.duration!=this[phase][index].duration));  
    this[phase][index] = newExercise;
  }
};

function adder(total, exercise) { return total + parseInt(exercise.duration) };

export { TrainingPlan };
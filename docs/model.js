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
  }

  duration() {
    return this.mainex.reduce( adder, this.ending.reduce( adder, this.warmup.reduce( adder, 0 ) ) );
  }

  static async loadData() {
    Disciplines = await loadObjectFromCSV('data/Disciplines.csv');
    Exercises = await loadObjectFromCSV('data/Exercises.csv',/;/);
    Exercises.Auslaufen = { id: "Auslaufen", name: "Auslaufen", warmup: false, runabc: false, mainex: false, ending: false, material: "", duration: "5", repeat: "2 Runden", disciplines: [], details: []};
  }

  static getAllDisciplines() {
    return Disciplines;
  }

  static generate(forDisciplineIds, targetDuration) {
    if (forDisciplineIds.length==0) return null;

    let suitableExercises = Object.values(Exercises).filter(
      // (exercise) => forDisciplineIds.filter( (selected) => exercise.disciplines.includes(selected) ).length > 0);
      (exercise) => forDisciplineIds.some( (selected) => exercise.disciplines.includes(selected) ));

    // console.log("Suitable: " + JSON.stringify(suitableExercises));

    let warmups = suitableExercises.filter( (exercise) => exercise.warmup );
    let runabcs = suitableExercises.filter( (exercise) => exercise.runabc );
    let mainexs = suitableExercises.filter( (exercise) => exercise.mainex );
    let endings = suitableExercises.filter( (exercise) => exercise.ending ); 
  
    // console.log(warmups.length + " Warm-ups: " + JSON.stringify(warmups));
    // console.log("RunABCs: " + JSON.stringify(runabcs));
    // console.log("Main exercises: " + JSON.stringify(mainexs));
    // console.log("Endings: " + JSON.stringify(endings));

    const forDisciplines = forDisciplineIds.map( (id) => Disciplines[id]);
    let plan = new TrainingPlan(forDisciplines);
    // the following algorithm is based purely on randomly picking exercises and does
    // not consider potential dependencies between exercises
    let attempts = 0;
    while((plan.duration()!=targetDuration) && (attempts++<10)) {
      console.log("Attempt " + attempts);
      // pick a random warmup and a random runabc
      plan.warmup = [ warmups.at(Math.floor(Math.random()*warmups.length)), runabcs.at(Math.floor(Math.random()*runabcs.length)) ];
      // pick a random ending and add the standard Auslaufen
      plan.ending = [ endings.at(Math.floor(Math.random()*endings.length)), Exercises.Auslaufen ];
      // pick main exercises until the target duration is reached or exceeded.
      plan.mainex = [];
      while(plan.duration()<=targetDuration-10) {
        let index = Math.floor(Math.random()*mainexs.length);
        let exerciseToAdd = mainexs.at(index);
        if(!plan.mainex.includes(exerciseToAdd)) {
          plan.mainex.push(exerciseToAdd);
        }
      }
    }
    if(attempts>=10) return undefined;
    return plan;
  }

  moveExerciseUp(exerciseId) {
    let index = this.mainex.findIndex( (exercise) => exercise.id==exerciseId )
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
    let index = this.mainex.findIndex( (exercise) => exercise.id==exerciseId )
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
};

function adder(total, exercise) { return total + parseInt(exercise.duration) };

export { TrainingPlan };
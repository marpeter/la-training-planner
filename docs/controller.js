import { TrainingPlan } from "./model.js";

document.addEventListener('DOMContentLoaded', function() {
  TrainingPlan.loadData().then( (result) => {
      console.log("Data loaded");
      let uiModel = {
        disciplines: TrainingPlan.getAllDisciplines(),
        selectedDisciplines: [],
        duration: 70,
        plan: undefined,
      };
      
      view.finishUi(uiModel);
      controller.registerEventHandlers();    
  });
});

const view = {

  model: undefined,

  finishUi(model) {
    this.model = model,
    this.createDisciplineCardList();
    this.updateShowButton();
  },

  // add the list of disciplines as clickable "chips" to the UI
  createDisciplineCardList() {
    Object.keys(this.model.disciplines).forEach( (discipline) => {
      let disciplineChip = document.createElement("div");
      disciplineChip.classList.add("chip", "red", "lighten-4");
      disciplineChip.id = discipline;
      if( this.model.disciplines[discipline].image) {
        let disciplineImage = document.createElement("img");
        disciplineImage.classList.add("disziplin");
        disciplineImage.src = this.model.disciplines[discipline].image;
        disciplineChip.appendChild(disciplineImage);
      }
      disciplineChip.appendChild(document.createTextNode(this.model.disciplines[discipline].name));
      disciplineChip.addEventListener("click", controller.onDisciplineSelected);
      document.getElementById("disziplinen").appendChild(disciplineChip);
   } );
  },
  
  // the "generate training plan" button should only be active if a disciplines is selected
  updateShowButton() {
    let showBtn = document.getElementById("showBtn");
    if(this.model.selectedDisciplines.length>0) {
      showBtn.classList.remove("disabled");
    } else {
      showBtn.classList.add("disabled");
    }
  },

  fillCardsForPhase(phaseName, exercises) {
    let phaseDiv = document.getElementById(phaseName);
    // clear the current phase content, add cards for the phase exercises
    while(phaseDiv.firstChild) { phaseDiv.removeChild(phaseDiv.firstChild);};
    if(phaseName==="mainex") {
      exercises.forEach( (exercise, index) => this.addExerciseCard(exercise, phaseDiv, index>0, index<exercises.length-1) );
    } else {
      exercises.forEach( (exercise) => this.addExerciseCard(exercise, phaseDiv, false, false) );
    }
  },

  addExerciseCard(exercise, toElement, isNotFirst, isNotLast) {
    let exerciseDiv = document.createElement("div");
    exerciseDiv.classList.add("col", "s12", "m6");
    let exerciseCard = document.createElement("div");
    exerciseCard.classList.add("card", "red", "lighten-4");
    let exerciseContent = document.createElement("div");
    exerciseContent.classList.add("card-content");
    let exerciseDescription = (exercise.details.length>0) ?
       `<span class="card-title center activator">${exercise.name}<i class="material-icons right">more_vert</i></span>` :
       `<span class="card-title center">${exercise.name}</span>`;
    exerciseDescription +=
      `<li>Material: ${exercise.material}</li>` +
      `<li>Dauer: ${exercise.duration}min</li>`+
      `<li>Wiederholungen: ${exercise.repeat}</li>`;
    exerciseContent.innerHTML = exerciseDescription;
    if(isNotFirst) {
      let moveUpButton = document.createElement("a");
      moveUpButton.classList.add("btn-floating", "red", "lighten-1", "left");
      moveUpButton.onclick = () => { controller.moveUp(exercise.id); };
      moveUpButton.innerHTML = '<i class="material-icons medium">arrow_upward</i>';
      exerciseContent.appendChild(moveUpButton);
    }
    if(isNotLast) {
      let moveDownButton = document.createElement("a");
      moveDownButton.classList.add("btn-floating", "red", "lighten-1", "right");
      moveDownButton.onclick = () => { controller.moveDown(exercise.id); };
      moveDownButton.innerHTML = '<i class="material-icons medium">arrow_downward</i>';
      exerciseContent.appendChild(moveDownButton);
    }
    exerciseCard.appendChild(exerciseContent);
    if (exercise.details.length>0) {
      let exerciseReveal = document.createElement("div");
      exerciseReveal.classList.add("card-reveal");
      exerciseReveal.innerHTML = '<span class="card-title grey-text text-darken-4">Details<i class="material-icons right">close</i></span>';
      exercise.details.forEach( (item) => exerciseReveal.innerHTML += `<li>${item}</li>`);
      exerciseCard.appendChild(exerciseReveal);
    }
    exerciseDiv.appendChild(exerciseCard);
    toElement.appendChild(exerciseDiv);
  }
};

const controller = {

  registerEventHandlers() {
    document.getElementById("showBtn").addEventListener("click", this.onCreatePlanButtonPressed);
    document.durationForm.duration.forEach( (radio) => radio.addEventListener("change", this.onDurationChanged));
  },

  onCreatePlanButtonPressed() {
    let plan = TrainingPlan.generate(view.model.selectedDisciplines, view.model.duration);
    if(!plan) {
      alert("Ich konnte keinen Plan finden / erzeugen :-(");
      return;
    }
    document.getElementById("plan-title").innerHTML = "Plan für "
      + plan.disciplines.map((discipline) => discipline.name ).join(" & ") + " (" + plan.duration() + "min)";  
    view.fillCardsForPhase("warmup", plan.warmup);
    view.fillCardsForPhase("mainex", plan.mainex);
    view.fillCardsForPhase("ending", plan.ending);
    view.model.plan = plan;
  },

  onDisciplineSelected(event) {
    let selectedDiscipline = (event.target.localName=='img') ?
        event.target.parentElement // click was on image -> discipline element is the parent element
      : event.target;
    let index = view.model.selectedDisciplines.indexOf(selectedDiscipline.id);
    if (index>=0) {
      // discipline was de-selected -> remove it from the list of selected disciplines
      view.model.selectedDisciplines.splice(index,1);
      selectedDiscipline.classList.remove("lighten-2");
      selectedDiscipline.classList.add("lighten-4");
    } else {
      selectedDiscipline.classList.remove("lighten-4");
      selectedDiscipline.classList.add("lighten-2");
      view.model.selectedDisciplines.push(selectedDiscipline.id);
    }
    view.updateShowButton();
  },

  onDurationChanged(event) {
    view.model.duration = event.target.value;
  },

  moveUp(exerciseId) {
    view.model.plan.moveExerciseUp(exerciseId);
    view.fillCardsForPhase("mainex", view.model.plan.mainex);
  },

  moveDown(exerciseId) {
    view.model.plan.moveExerciseDown(exerciseId);
    view.fillCardsForPhase("mainex", view.model.plan.mainex);
  }
};

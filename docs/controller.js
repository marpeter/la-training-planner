import { TrainingPlan } from "./model.js";

document.addEventListener('DOMContentLoaded', function() {
  TrainingPlan.loadData().then( (result) => {
      let uiModel = {
        disciplines: TrainingPlan.getAllDisciplines(),
        version: TrainingPlan.version,
        selectedDisciplines: [],
        duration: 70,
        plan: undefined, // the plan shown - initially undefined
        favorites: [], // the list of available favorites matching the selected disciplines and duration
        selectedFavorite: undefined, // index of the selected favorite if the plan was loaded from favorites
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
    this.updateAfterCriteriaChanged();
    this.updateVersion();
  },

  // add the list of disciplines as clickable "chips" to the UI
  createDisciplineCardList() {
    this.model.disciplines.forEach( (discipline) => {
      let disciplineChip = document.createElement("div");
      disciplineChip.classList.add("chip", "red", "lighten-4");
      disciplineChip.id = discipline.id;
      if( discipline.image) {
        let disciplineImage = document.createElement("img");
        disciplineImage.classList.add("disziplin");
        disciplineImage.src = discipline.image;
        disciplineChip.appendChild(disciplineImage);
      }
      disciplineChip.appendChild(document.createTextNode(discipline.name));
      disciplineChip.addEventListener("click", controller.onDisciplineSelected);
      document.getElementById("disziplinen").appendChild(disciplineChip);
   } );
  },
  
  // the "generate training plan" button should only be active if a disciplines is selected
  updateAfterCriteriaChanged() {
    view.model.favorites = TrainingPlan.getAvailableFavorites(view.model.selectedDisciplines, view.model.duration);

    let gnrtBtn = document.getElementById("gnrtBtn");
    let loadBtn = document.getElementById("loadBtn");
    let prevBtn = document.getElementById("prevBtn");
    let nextBtn = document.getElementById("nextBtn");
    if(this.model.selectedDisciplines.length>0) {
      gnrtBtn.classList.remove("disabled");

    } else {
      gnrtBtn.classList.add("disabled");
      loadBtn.classList.add("disabled");
    }
    if(view.model.favorites.length>0) {
      loadBtn.classList.remove("disabled");
    } else {
      loadBtn.classList.add("disabled");
    }
    if(this.model.selectedFavorite===undefined || view.model.favorites.length<2 ) {
      // no favorite selected or there is only one --> paging makes no sense
      prevBtn.classList.add("disabled");
      nextBtn.classList.add("disabled");
    } else { // favorite selected --> paging is possible
      prevBtn.classList.remove("disabled");
      nextBtn.classList.remove("disabled");
    }
    loadBtn.innerHTML = `Lade Favoriten (${view.model.favorites.length})`;
  },

  // update the version number in the footer of the page and enable the edit button if the version supports editing
  updateVersion() {
    let versionElement = document.getElementById("version");
    versionElement.innerHTML = this.model.version.number;
    if(this.model.version.supportsEditing) {
      let editBtn = document.getElementById("editBtn");
      editBtn.classList.remove("disabled");
    }
  },

  // update the plan title and the cards for each phase
  updatePlan() {
    if(this.model.selectedFavorite!==undefined) this.model.plan = this.model.favorites[this.model.selectedFavorite];
    document.getElementById("plan-title").innerHTML =
     `Plan ${this.model.plan.description} für ${this.model.plan.disciplines.map((discipline) => discipline.name ).join(" & ")}` +
     ` (${this.model.plan.duration()}min)`;  
    this.fillCardsForPhase("warmup", this.model.plan.warmup);
    this.fillCardsForPhase("mainex", this.model.plan.mainex);
    this.fillCardsForPhase("ending", this.model.plan.ending);
  },

  fillCardsForPhase(phaseName, exercises) {
    let phaseDiv = document.getElementById(phaseName);
    // clear the current phase content, add cards for the phase exercises
    while(phaseDiv.firstChild) { phaseDiv.removeChild(phaseDiv.firstChild);};
    exercises.forEach( (exercise, index) => this.addExerciseCard(exercise, phaseName, index==0, index==exercises.length-1) );
  },

  addExerciseCard(exercise, phase, isFirst, isLast) {
    let toElement = document.getElementById(phase);
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
      `<li>Wiederholungen: ${exercise.repeats}</li>`;
    exerciseContent.innerHTML = exerciseDescription;
    if(phase==="mainex") {
      let moveUpButton = this.createFloatingButton("arrow_upward", isFirst);
      moveUpButton.onclick = () => { controller.moveUp(exercise.id); };
      exerciseContent.appendChild(moveUpButton);
    }
    if(!exercise.sticky) {
      let replaceButton = this.createFloatingButton("change_circle");
      replaceButton.onclick = () => { controller.replace(phase, exercise.id); };
      exerciseContent.appendChild(replaceButton);
    }
    if(phase==="mainex") {
      let moveDownButton = this.createFloatingButton("arrow_downward", isLast);
      moveDownButton.onclick = () => { controller.moveDown(exercise.id); };
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
  },

  createFloatingButton(icon, disabled=false) {
    let floatingButton = document.createElement("a");
    floatingButton.classList.add("btn-floating", "red", "lighten-1", "right");
    if(disabled) floatingButton.classList.add("disabled");
    floatingButton.innerHTML = `<i class="large material-icons">${icon}</i>`;
    return floatingButton;
  },

  updateMessages(messages) {
    let messageDiv = document.getElementById("messages");
    while(messageDiv.firstChild) { messageDiv.removeChild(messageDiv.firstChild);};
    if(messages.length>0) {
      let messageCard = document.createElement("div");
      messageCard.classList.add("card-panel");
      messageCard.innerHTML = messages.join("<br>");
      messageDiv.appendChild(messageCard);
    }
  },

};

const controller = {

  registerEventHandlers() {
    document.getElementById("gnrtBtn").addEventListener("click", this.onCreatePlanButtonPressed);
    document.getElementById("loadBtn").addEventListener("click", this.onLoadPlanButtonPressed);
    document.getElementById("prevBtn").addEventListener("click", this.onPrevBtnPressed);
    document.getElementById("nextBtn").addEventListener("click", this.onNextBtnPressed);
    document.durationForm.duration.forEach( (radio) => radio.addEventListener("change", this.onDurationChanged));
  },

  onCreatePlanButtonPressed() {
    let plan = TrainingPlan.generate(view.model.selectedDisciplines, view.model.duration);
    view.updateMessages(TrainingPlan.messages);
    if(!plan) {
      return;
    } else {
      view.model.plan = plan;
      view.model.selectedFavorite = undefined;
      view.updatePlan();
    }
  },

  onLoadPlanButtonPressed() {
    if(view.model.favorites.length>0) {
      view.model.selectedFavorite = 0;
      view.updateAfterCriteriaChanged();
      view.updatePlan();
    }
  },

  onPrevBtnPressed() {
    if(view.model.favorites.length>0) {
      view.model.selectedFavorite = (view.model.selectedFavorite-1 + view.model.favorites.length) % view.model.favorites.length;
      view.updatePlan();
    }
  },

  onNextBtnPressed() {
    if(view.model.favorites.length>0) {
      view.model.selectedFavorite = (view.model.selectedFavorite+1) % view.model.favorites.length;
      view.updatePlan();
    }
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
    view.updateAfterCriteriaChanged();
  },

  onDurationChanged(event) {
    view.model.duration = event.target.value;
    view.model.selectedFavorite = undefined;
    view.updateAfterCriteriaChanged();
  },

  moveUp(exerciseId) {
    view.model.plan.moveExerciseUp(exerciseId);
    view.fillCardsForPhase("mainex", view.model.plan.mainex);
  },

  moveDown(exerciseId) {
    view.model.plan.moveExerciseDown(exerciseId);
    view.fillCardsForPhase("mainex", view.model.plan.mainex);
  },

  replace(phase, exerciseId) {
    view.model.plan.replaceExercise(phase, exerciseId);
    view.fillCardsForPhase(phase, view.model.plan[phase]);
  }
};

import { App, Discipline, TrainingPlan } from "./model.js";
import { updateCommonUiElements } from "./common.js";

document.addEventListener('DOMContentLoaded', function() {
  App.loadData().then( (result) => {

    if( App.version.withBackend && !App.version.withDB ) {
      window.location = "admin/setup.php";
    }

    view.finishUi({
      disciplines: Discipline.getAll(),
      version: App.version,
      selectedDisciplines: [],
      duration: 70,
      plan: undefined, // the plan shown - initially undefined
      favorites: [], // the list of available favorites matching the selected disciplines and duration
      selectedFavorite: undefined, // index of the selected favorite if the plan was loaded from favorites
    });
    controller.registerEventHandlers();
    controller.onCriteriaChanged();    
  });
});

const view = {

  model: undefined,
  generatePlanButton: undefined,
  loadPlanButton: undefined,
  prevPlanButton: undefined,
  nextPlanButton: undefined,
  savePlanButton: undefined,
  savePlanAsButton: undefined,
  deletePlanButton: undefined,

  finishUi(model) {
    this.model = model,
    this.createDisciplineCardList();

    this.generatePlanButton = document.getElementById("generate-plan");
    this.loadPlanButton = document.getElementById("load-plan");
    this.prevPlanButton = document.getElementById("prev-plan");
    this.nextPlanButton = document.getElementById("next-plan");
    this.savePlanButton = document.getElementById("save-plan");
    this.savePlanAsButton = document.getElementById("save-plan-as");
    this.deletePlanButton = document.getElementById("delete-plan");

    updateCommonUiElements(this.model.version);
  },

  // add the list of disciplines as clickable "chips" to the UI
  createDisciplineCardList() {
    this.model.disciplines.forEach( (discipline) => {
      let disciplineChip = document.createElement("div");
      disciplineChip.classList.add("chip", "tfat", "lighten-4");
      disciplineChip.id = discipline.id;
      if(discipline.image) {
        let disciplineImage = document.createElement("img");
        disciplineImage.classList.add("disziplin");
        disciplineImage.src = discipline.image;
        disciplineChip.appendChild(disciplineImage);
      }
      disciplineChip.appendChild(document.createTextNode(discipline.name));
      disciplineChip.onclick = () => { controller.onDisciplineSelected(discipline.id); };
      document.getElementById("disziplinen").appendChild(disciplineChip);
   });
  },

  // update the plan title and the cards for each phase
  updatePlan() {
    if(this.model.selectedFavorite!==undefined) {
      this.model.plan = this.model.favorites[this.model.selectedFavorite];
    }
    this.updatePlanTitle();
    this.updatePlanPhase("warmup", this.model.plan ? this.model.plan.warmup : []);
    this.updatePlanPhase("mainex", this.model.plan ? this.model.plan.mainex : []);
    this.updatePlanPhase("ending", this.model.plan ? this.model.plan.ending : []);
  },

  updatePlanTitle() {
    if(this.model.plan) {
      document.getElementById("plan-title").innerHTML =
       `Plan ${this.model.plan.description} ${this.model.plan.isModified() ? "(ed.)" : ""} für `+
       this.model.plan.disciplines.map((discipline) => discipline.name ).join(" & ") +
       ` (${this.model.plan.duration()}min)`;
    } else { 
      document.getElementById("plan-title").innerHTML = "Bitte erzeuge oder lade einen Plan";
    }

  },

  updatePlanPhase(phaseName, exercises) {
    let phaseDiv = document.getElementById(phaseName);
    // clear the current phase content, add cards for the phase exercises
    while(phaseDiv.firstChild) { phaseDiv.removeChild(phaseDiv.firstChild);};
    exercises.forEach( (exercise, index) =>
      this.addExerciseCard(exercise, phaseName, index==0, index==exercises.length-1) );
  },

  addExerciseCard(exercise, phase, isFirst, isLast) {
    let exerciseDiv = document.createElement("div");
    exerciseDiv.classList.add("col", "s12", "m6");
    let exerciseCard = document.createElement("div");
    exerciseCard.classList.add("card", "tfat", "lighten-4");
    let exerciseContent = document.createElement("div");
    exerciseContent.classList.add("card-content");
    let exerciseDescription = (exercise.details.length>0) ?
       `<span class="card-title center activator">${exercise.name}<i class="material-icons right">more_vert</i></span>`
       : `<span class="card-title center">${exercise.name}</span>`;
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
      exerciseReveal.classList.add("card-reveal", "tfat", "lighten-5");
      exerciseReveal.innerHTML = '<span class="card-title grey-text text-darken-4">Details<i class="material-icons right">close</i></span>';
      exercise.details.forEach( (item) => exerciseReveal.innerHTML += `<li>${item}</li>`);
      exerciseCard.appendChild(exerciseReveal);
    }
    exerciseDiv.appendChild(exerciseCard);
    document.getElementById(phase).appendChild(exerciseDiv);
  },

  createFloatingButton(icon, disabled=false) {
    let floatingButton = document.createElement("a");
    floatingButton.classList.add("btn-floating", "tfat", "lighten-1", "right");
    if(disabled) floatingButton.classList.add("disabled");
    floatingButton.innerHTML = `<i class="large material-icons">${icon}</i>`;
    return floatingButton;
  },

  updateMessages(messages) {
    if(messages.length===0) return;
    M.toast({html: messages.join("<br>"), classes: "red accent-3 rounded"});
  },
};

const controller = {

  registerEventHandlers() {
    document.getElementById("generate-plan").onclick = this.onCreatePlanButtonPressed;
    document.getElementById("load-plan").onclick = this.onLoadPlanButtonPressed;
    document.getElementById("prev-plan").onclick = this.onPrevBtnPressed;
    document.getElementById("next-plan").onclick = this.onNextBtnPressed;
    document.getElementById("save-plan").onclick = this.onSavePlanBtnPressed;
    document.getElementById("save-plan-as").onclick = this.onSavePlanAsBtnPressed;
    document.getElementById("delete-plan").onclick = this.onDeletePlanBtnPressed;
    
    document.durationForm.duration.forEach( (radio) => radio.onchange = this.onDurationChanged);
  },

  onCreatePlanButtonPressed() {
    let { plan: plan, messages: messages } = TrainingPlan.generate(view.model.selectedDisciplines, view.model.duration);
    view.updateMessages(messages);
    if(plan) {
      view.model.plan = plan;
      view.model.selectedFavorite = undefined;
      view.updatePlan();
      controller.updateFavoritesButtons();
      controller.resetFavoriteSaveDeleteButtons();
    }
  },

  onLoadPlanButtonPressed() {
    if(view.model.favorites.length>0) {
      view.model.selectedFavorite = 0;
      view.updatePlan();
      controller.updateFavoritesButtons();
      controller.resetFavoriteSaveDeleteButtons();
    }
  },

  onPrevBtnPressed() {
    if(view.model.favorites.length>0) {
      view.model.selectedFavorite = (view.model.selectedFavorite-1 + view.model.favorites.length) % view.model.favorites.length;
      view.updatePlan();
      controller.resetFavoriteSaveDeleteButtons();
    }
  },

  onNextBtnPressed() {
    if(view.model.favorites.length>0) {
      view.model.selectedFavorite = (view.model.selectedFavorite+1) % view.model.favorites.length;
      view.updatePlan();
      controller.resetFavoriteSaveDeleteButtons();
    }
  },

  // update the UI after criteria such as selected disciplines or duration have changed
  onCriteriaChanged() {
    view.model.favorites = TrainingPlan.getAvailableFavorites(
      view.model.selectedDisciplines, view.model.duration);
    controller.updateGeneratePlanButton();
    controller.updateFavoritesButtons();
  },

  updateGeneratePlanButton() {
    // the "generate training plan" button should only be active if a disciplines is selected  
    if(view.model.selectedDisciplines.length>0) {
      view.generatePlanButton.classList.remove("disabled");
    } else {
      view.generatePlanButton.classList.add("disabled");
    }
  },

  updateFavoritesButtons() {
    if(view.model.favorites.length>0) {
      view.loadPlanButton.classList.remove("disabled");
    } else {
      view.loadPlanButton.classList.add("disabled");
    }
    view.loadPlanButton.innerHTML = `Lade Favoriten (${view.model.favorites.length})`;

    if(view.model.selectedFavorite===undefined || view.model.favorites.length<2 ) {
      // no favorite selected or there is only one --> paging makes no sense
      view.prevPlanButton.classList.add("disabled");
      view.nextPlanButton.classList.add("disabled");
    } else { // favorite selected and there are more --> paging is possible
      view.prevPlanButton.classList.remove("disabled");
      view.nextPlanButton.classList.remove("disabled");
    }
    controller.resetFavoriteSaveDeleteButtons();
  },

  resetFavoriteSaveDeleteButtons() {
    if(!view.model.version.supportsEditing) return;

    if(view.model.selectedFavorite===undefined) {
      view.deletePlanButton.classList.add("disabled");
      if(view.model.plan !== undefined){
        view.savePlanButton.classList.add("disabled");
        view.savePlanAsButton.classList.remove("disabled");
      }
    } else {
      view.deletePlanButton.classList.remove("disabled");
      if(view.model.plan.isModified()) {
        view.savePlanButton.classList.remove("disabled");
        view.savePlanAsButton.classList.remove("disabled");
      } else {
        view.savePlanButton.classList.add("disabled");
        view.savePlanAsButton.classList.add("disabled");
      }
    }
  },

  updateOnFavoriteModified() {
    view.updatePlanTitle();
    controller.resetFavoriteSaveDeleteButtons();
  },

  onDisciplineSelected(disciplineId) {
    let selectedDiscipline = document.getElementById(disciplineId);
    let index = view.model.selectedDisciplines.indexOf(disciplineId);
    if (index>=0) {
      // discipline was de-selected -> remove it from the list of selected disciplines
      view.model.selectedDisciplines.splice(index,1);
      selectedDiscipline.classList.remove("lighten-2");
      selectedDiscipline.classList.add("lighten-4");
    } else {
      selectedDiscipline.classList.remove("lighten-4");
      selectedDiscipline.classList.add("lighten-2");
      view.model.selectedDisciplines.push(disciplineId);
    }
    controller.onCriteriaChanged();
  },

  onDurationChanged(event) {
    view.model.duration = event.target.value;
    view.model.selectedFavorite = undefined;
    controller.onCriteriaChanged();
  },

  moveUp(exerciseId) {
    view.model.plan.moveExerciseUp(exerciseId);
    view.updatePlanPhase("mainex", view.model.plan.mainex);
    controller.updateOnFavoriteModified();
  },

  moveDown(exerciseId) {
    view.model.plan.moveExerciseDown(exerciseId);
    view.updatePlanPhase("mainex", view.model.plan.mainex);
    controller.updateOnFavoriteModified();
  },

  replace(phase, exerciseId) {
    view.model.plan.replaceExercise(phase, exerciseId);
    view.updatePlanPhase(phase, view.model.plan[phase]);
    controller.updateOnFavoriteModified();
  },

  onSavePlanBtnPressed() {
    if(view.model.selectedFavorite===undefined || view.model.plan===undefined) {
      M.toast({html: "Es gibt keinen Plan zum Speichern!", classes: "tfat-error rounded"});
      return;
    }
    view.model.plan.save().then( (data) => {
      if(data.success) {
        M.toast({html: "Plan erfolgreich gespeichert.", classes: "tfat-success rounded"});
        // reload favorites and update UI
        view.model.favorites = TrainingPlan.getAvailableFavorites(view.model.selectedDisciplines, view.model.duration);
        view.model.selectedFavorite = view.model.favorites.findIndex(fav => fav.id == data.message.id);
        view.model.plan = view.model.favorites[view.model.selectedFavorite];
        view.updatePlan();
        controller.resetFavoriteSaveDeleteButtons();
      } else {
        M.toast({html: "Fehler beim Speichern des Plans: " + data.message, classes: "tfat-error rounded"});
      }
    }).catch(error => {
      console.error("Error saving exercise:", error);
      M.toast({html: "Fehler beim Speichern des Plans. " + error, classes: "tfat-error rounded"});
    });
  },

  onSavePlanAsBtnPressed() {
    let description = prompt("Bitte gib eine Beschreibung für den Plan ein:", view.model.plan.description);
    if(description===null) {
      return; // user cancelled the prompt
    }
    view.model.plan.saveAs(description)
    .then( (data) => {
      if(data.success) {
        M.toast({html: "Plan erfolgreich gespeichert.", classes: "tfat-success rounded"});
        // reload favorites and update UI
        view.model.favorites = TrainingPlan.getAvailableFavorites(view.model.selectedDisciplines, view.model.duration);
        view.model.selectedFavorite = view.model.favorites.findIndex(fav => fav.id == data.message.id);
        view.model.plan = view.model.favorites[view.model.selectedFavorite];
        view.updatePlan();
        controller.updateFavoritesButtons();;
      } else {
        M.toast({html: "Fehler beim Speichern des Plans: " + data.message, classes: "tfat-error rounded"});
      }
    }).catch(error => {
      console.error("Error saving exercise:", error);
      M.toast({html: "Fehler beim Speichern des Plans. " + error, classes: "tfat-error rounded"});
    });
  },

  onDeletePlanBtnPressed() {
    if(view.model.selectedFavorite===undefined) {
      M.toast({html: "Es gibt keinen Plan zum Löschen!", classes: "tfat-error rounded"});
      return;
    }
    if(confirm("Soll der Plan wirklich gelöscht werden?")) {
      view.model.plan.delete().then( (data) => {
        if(data.success) {
          M.toast({html: "Plan erfolgreich gelöscht.", classes: "tfat-success rounded"});
          view.model.selectedFavorite = undefined;
          view.model.plan = undefined; // clear the current plan
          view.updatePlan();
          controller.onCriteriaChanged(); // reload favorites and update UI
        } else {
          M.toast({html: "Fehler beim Löschen des Plans: " + data.message, classes: "tfat-error rounded"});
        }
      }).catch(error => {
        console.error("Error saving exercise:", error);
        M.toast({html: "Fehler beim Löschen des Plans. " + error, classes: "tfat-error rounded"});
      });
    }
  }
};

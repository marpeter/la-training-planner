import { App, Discipline, Exercise, TrainingPlan } from "../model.js";

document.addEventListener('DOMContentLoaded', () => {
  App.loadData("../").then( (result) => {
    view.finishUi({
      version: App.version,
      disciplines: Discipline.getAll(),
      selectedExercise: undefined,
      gotoExercise: undefined, 
      exerciseListFilter: "",
    });
    controller.registerEventHandlers();    
  });
 });

const NULL_EXERCISE = new Exercise("", "", []);

const view = {
  model: undefined,
  // properties to remember elements of the exercise form
  exerciseId: undefined,
  exerciseName: undefined,
  exercisePhases: undefined,
  exerciseDisciplines: undefined,
  exerciseMaterial: undefined,
  exerciseDurationMin: undefined,
  exerciseDurationMax: undefined,
  exerciseReps: undefined,
  exerciseDetails: undefined,

  finishUi(model) {
    this.model = model;
    this.setVersionInfo();
    this.fillDisciplineSelect();
      
    this.exerciseId = document.getElementById("exercise-id");
    this.exerciseName = document.getElementById("exercise-name");
    this.exercisePhases = document.getElementById("exercise-phases");
    this.exerciseDisciplines = document.getElementById("exercise-disciplines");
    this.exerciseMaterial = document.getElementById("exercise-material");
    this.exerciseDurationMin = document.getElementById("exercise-duration-min");
    this.exerciseDurationMax = document.getElementById("exercise-duration-max");
    this.exerciseReps = document.getElementById("exercise-reps");
    this.exerciseDetails = document.getElementById("exercise-details");

    this.fillExerciseList();
    this.updateExerciseForm();
  },

   // fill the discipline select with the disciplines from the model
  fillDisciplineSelect() {
    let disciplineSelect = document.getElementById("exercise-disciplines");
    this.model.disciplines.forEach( (discipline) => {
      let option = document.createElement("option");
      option.value = discipline.id;
      option.innerHTML = discipline.name;
      disciplineSelect.appendChild(option);
    });
  },

  // update the version number in the footer of the page and enable the edit button if the version supports editing
  setVersionInfo() {
    document.getElementById("version").innerHTML = this.model.version.number;
  }, 

  // fill the exercise list with the exercises from the model
  fillExerciseList() {
    let exerciseList = document.getElementById("exercise-list");
    // clear the list, keeping the header (first child)
    while(exerciseList.childElementCount > 1) { exerciseList.removeChild(exerciseList.lastChild);}

    Exercise.getAll().forEach( (exercise) => {
      // skip the "Auslaufen" exercise, it is always the last one
      if (!exercise.sticky && exercise.name.startsWith(this.model.exerciseListFilter)) {
        let item = document.createElement("li");
        item.classList.add("collection-item", "left-align");
        let anchor = document.createElement("a");
        anchor.classList.add("red-text");
        anchor.innerHTML = exercise.name;
        anchor.setAttribute("href", "#!");
        anchor.setAttribute("data-id", exercise.id);
        anchor.onclick = () => controller.onExerciseSelected(exercise.id);
        item.appendChild(anchor);
        exerciseList.appendChild(item);
      }
    });
  },

  // update the exerise form with the selected exercise
  updateExerciseForm() {
    let enable, copying;
    let exercise = this.model.selectedExercise;
    if (exercise === undefined) {
      enable = copying = false;
      exercise = NULL_EXERCISE;
    } else {
      // enable the form only if the app version supports editing
      enable = this.model.version.supportsEditing;
      copying = exercise.isNotInDb();
    }

    this.exerciseId.value = exercise.id;
    this.exerciseName.value = exercise.name;
    this.exerciseMaterial.value = exercise.material;
    this.exerciseDurationMin.value = exercise.durationmin;
    this.exerciseDurationMax.value = exercise.durationmax;
    this.exerciseReps.value = exercise.repeats;
    this.exerciseDetails.value = Array.isArray(exercise.details) ? exercise.details.join(':') : exercise.details;
    Array.from(this.exercisePhases.children)
         .forEach( (option) => option.selected = exercise[option.value] );
    Array.from(this.exerciseDisciplines.children)
         .forEach( (option) => option.selected = exercise.disciplines.includes(option.value) );
    
    this.exerciseId.disabled = !copying;
    this.exerciseName.disabled =
    this.exercisePhases.disabled =
    this.exerciseDisciplines.disabled =
    this.exerciseMaterial.disabled =
    this.exerciseDurationMin.disabled =
    this.exerciseDurationMax.disabled =
    this.exerciseReps.disabled =
    this.exerciseDetails.disabled = !enable;
    
    if (enable) {
      document.getElementById("save-exercise").classList.remove("disabled");
      if(copying) {
        document.getElementById("copy-exercise").classList.add("disabled");
        document.getElementById("delete-exercise").classList.add("disabled");
      } else {
        document.getElementById("copy-exercise").classList.remove("disabled");
        document.getElementById("delete-exercise").classList.remove("disabled")
      }
    } else {
      document.getElementById("save-exercise").classList.add("disabled");
      document.getElementById("copy-exercise").classList.add("disabled");
      document.getElementById("delete-exercise").classList.add("disabled");
    }

    M.updateTextFields();
    M.FormSelect.init(document.getElementById("exercise-disciplines"));
    M.FormSelect.init(document.getElementById("exercise-phases"));
  },
}

const controller = {
    registerEventHandlers() {
      document.getElementById("exercise-filter").oninput = this.onExcerciseFilterChanged;
      document.getElementById("exercise-duration-min").onchange = this.checkExerciseDuration;
      document.getElementById("exercise-duration-max").onchange = this.checkExerciseDuration;
      document.getElementById("exercise-edit").onchange = this.checkExerciseEditForm;
      document.getElementById("save-exercise").onclick = this.onSaveExercise;
      document.getElementById("copy-exercise").onclick = this.onCopyExercise;
      document.getElementById("delete-exercise").onclick = this.onDeleteExercise;
      // initialize the static select elements
      M.FormSelect.init(document.querySelectorAll('select'));
      // initialize the dynamic select elements
      M.FormSelect.init(document.getElementById("exercise-disciplines"));
      // initialize the modal for the delete confirmation
      M.Modal.init(document.querySelectorAll('.modal'));
      document.getElementById("confirm-delete-yes").onclick = this.onDeleteExerciseConfirmed;
      document.getElementById("confirm-delete-no").onclick = this.onDeleteExerciseCancelled;
      document.getElementById("confirm-save-yes").onclick = this.onSaveChanges;
      document.getElementById("confirm-save-no").onclick = this.onDiscardChanges;

      // So far no login screen available ...
      document.getElementById("loginBtn").classList.add("disabled");
      console.log(document.getElementById("loginBtn").classList);
    },

    onExcerciseFilterChanged(event) {
      view.model.exerciseListFilter = event.target.value;
      view.fillExerciseList();
    },

    onExerciseSelected(exerciseId) {
      view.model.gotoExercise = Exercise.getAll().find( (exercise) => exercise.id==exerciseId );
      // check if there are unsaved changes and if so, ask the user if they want to save them
      if(controller.unsavedChanges()) {
        let confirmDialog = M.Modal.getInstance(document.getElementById("save-edits"));
        confirmDialog.options.dismissible = false;
        confirmDialog.open();
      } else {
        controller.switchToExerciseSelected();
      }
    },

    unsavedChanges() {
      if (view.model.selectedExercise === undefined) {
        return false; // no exercise selected => no unsaved changes
      } else {
        // check if the exercise data on the form is different from the original (selected) exercise
        // note that validity of the form is checked only before saving
        let exerciseOnForm = controller.getExerciseFromForm();
        return view.model.selectedExercise.isNotInDb() || !view.model.selectedExercise.equals(exerciseOnForm)
      }
    },

    switchToExerciseSelected() {
      view.model.selectedExercise = view.model.gotoExercise;
      view.updateExerciseForm();
    },

    getExerciseFromForm() {
      let phases = Array.from(view.exercisePhases.selectedOptions).map(option => option.value);
      let exerciseOnForm = new Exercise(
        view.exerciseId.value,
        view.exerciseName.value,
        Array.from(view.exerciseDisciplines.selectedOptions).map(option => option.value),
        parseInt(view.exerciseDurationMin.value, 10),
        parseInt(view.exerciseDurationMax.value, 10),
        phases.includes("warmup"),
        phases.includes("runabc"),
        phases.includes("mainex"),
        phases.includes("ending"),
        view.exerciseReps.value,
        view.exerciseMaterial.value,
        Array.from(view.exerciseDetails.value.split(':'))
      );
      exerciseOnForm.sticky = view.model.selectedExercise.sticky;
      return exerciseOnForm;
    },

    async onSaveChanges(event) {
      // save the changes
      controller.saveExerciseChanges().then( () => {
        // after saving, switch to the selected exercise, same as discarding changes
        controller.onDiscardChanges(event);
      });
    },

    onDiscardChanges(event) {
      M.Modal.getInstance(document.getElementById("save-edits"))
             .close();
      controller.switchToExerciseSelected(); // switch to the exercise selected before the save dialog was opened
    },

    checkExerciseDuration(event) {
      if (parseInt(view.exerciseDurationMin.value, 10) > parseInt(view.exerciseDurationMax.value, 10)) {
        view.exerciseDurationMin.setCustomValidity("Die minimale Dauer muss kleiner sein als die maximale Dauer.");
        return false;
      } else {
        view.exerciseDurationMin.setCustomValidity("");
        return true;
      }
    },

    checkExerciseEditForm(event) {
      let okay = true;
      let helper = document.getElementById("exercise-phases-helper"); 
      if(view.exercisePhases.value === "") {
        helper.classList.add("red-text");
        okay = false;
      } else {
        helper.classList.remove("red-text");
      }
      helper = document.getElementById("exercise-disciplines-helper");
      if(view.exerciseDisciplines.value === "") {
        helper.classList.add("red-text");
        okay = false;
      } else {
        helper.classList.remove("red-text");
      }
      return okay;
    },

    saveExerciseChanges() {
      let okay = document.forms["exercise-edit"].checkValidity() &&
                 controller.checkExerciseDuration() &&
                 controller.checkExerciseEditForm();
      if(!okay) {
        M.toast({html: "Bitte fülle alle Felder korrekt aus.", classes: "red accent-3 rounded"});
      } else {
        let modifiedExercise = controller.getExerciseFromForm();
        return modifiedExercise.save()
        .then(data => {
          if(data.success) {
            M.toast({html: "Übung erfolgreich gespeichert.", classes: "green accent-3 rounded"});
          } else {
            console.error("Error saving exercise:", data);
            M.toast({html: "Fehler beim Speichern der Übung: " + data.message, classes: "red accent-3 rounded"});
          }
        })
        .then( () =>  view.fillExerciseList() ) // update the exercise list in case an exercise was copied or a name changed
        .catch(error => {
          console.error("Error saving exercise:", error);
          M.toast({html: "Fehler beim Speichern der Übung.", classes: "red accent-3 rounded"});
        });
      }
    },

    onSaveExercise() {
      controller.saveExerciseChanges().then( () => {
        view.model.selectedExercise = Exercise.getAll().find(exercise => exercise.id === view.model.selectedExercise.id);
        view.updateExerciseForm(); // update the form to reflect the changes
      });
    },

    onCopyExercise() {
      if (view.model.selectedExercise === undefined) { return; }
      view.model.selectedExercise = view.model.selectedExercise.copy(); // set the copied exercise as the selected exercise
      view.updateExerciseForm();
    },
      
    onDeleteExercise() {
      let confirmDialog = M.Modal.getInstance(document.getElementById("confirm-delete"));
      confirmDialog.options.dismissible = false;
      // Determine the favorite plans the exercise is part of
      let favoritePlans = view.model.selectedExercise.containedInFavoritePlans();
      document.getElementById("confirm-delete-content").innerHTML = 
        `<h4>Übung löschen.</h4><p>Soll die Übung <strong>${view.model.selectedExercise.name}</strong> wirklich gelöscht werden?</p>`
        + ( favoritePlans.length === 0 ? 
            "<p>Die Übung ist in keinem Favoriten-Trainingsplan enthalten.</p>" :
            `<p class="red-text">Die Übung ist in Favoriten-Trainingsplänen enthalten:</br>${favoritePlans.map( plan => plan.description ).join(', ')}</p>`
          );
      confirmDialog.open();
    },

    onDeleteExerciseConfirmed() {
      M.Modal.getInstance(document.getElementById("confirm-delete"))
             .close();
      view.model.selectedExercise.delete()
      .then(data => {
        if(data.success) {
          M.toast({html: "Übung erfolgreich gelöscht.", classes: "green accent-3 rounded"});
          // delete the exercise in the model
          // App.loadData("../").then( (result) => {  
          view.model.selectedExercise = undefined; // clear the selected exercise
          view.fillExerciseList();
          view.updateExerciseForm();
        } else {
          M.toast({html: "Fehler beim Löschen der Übung: " + data.message, classes: "red accent-3 rounded"});
        }
      }).catch(error => {
        console.error("Error saving exercise:", error);
        M.toast({html: "Fehler beim Löschen der Übung.", classes: "red accent-3 rounded"});
      }); 
    },

    onDeleteExerciseCancelled() {
      M.Modal.getInstance(document.getElementById("confirm-delete"))
             .close();
    },
}
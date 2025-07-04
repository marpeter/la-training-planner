import { App, Discipline, Exercise, TrainingPlan } from "../model.js";

document.addEventListener('DOMContentLoaded', () => {
    App.loadData("../").then( (result) => {
        view.finishUi({
          version: App.version,
          disciplines: Discipline.getAll(),
          selectedExercise: undefined, 
          exerciseListFilter: "",
          copying: false,
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
    let enable;
    let exercise = this.model.selectedExercise;
    if (exercise === undefined) {
      enable = false;
      exercise = NULL_EXERCISE;
    } else {
      // enable the form only if the app version supports editing
      enable = this.model.version.supportsEditing;
    }

    this.exerciseId.value = exercise.id;
    this.exerciseName.value = exercise.name;
    this.exerciseMaterial.value = exercise.material;
    this.exerciseDurationMin.value = exercise.durationmin;
    this.exerciseDurationMax.value = exercise.durationmax;
    this.exerciseReps.value = exercise.repeats;
    this.exerciseDetails.value = exercise.details;
    Array.from(this.exercisePhases.children)
         .forEach( (option) => option.selected = exercise[option.value] );
    Array.from(this.exerciseDisciplines.children)
         .forEach( (option) => option.selected = exercise.disciplines.includes(option.value) );
    
    this.exerciseId.disabled = !this.model.copying;
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
      if( this.model.copying) {
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
    },

    onExcerciseFilterChanged(event) {
      view.model.exerciseListFilter = event.target.value;
      view.fillExerciseList();
    },

    onExerciseSelected(exerciseId) {
      let exercise = Exercise.getAll().find( (exercise) => exercise.id==exerciseId );
      view.model.selectedExercise = exercise;
      view.model.copying = false; // reset the copying flag
      view.updateExerciseForm();
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
      if(document.getElementById("exercise-phases").value === "") {
        helper.classList.add("red-text");
        okay = false;
      } else {
        helper.classList.remove("red-text");
      }
      helper = document.getElementById("exercise-disciplines-helper");
      if(document.getElementById("exercise-disciplines").value === "") {
        helper.classList.add("red-text");
        okay = false;
      } else {
        helper.classList.remove("red-text");
      }
      return okay;
    },

    onSaveExercise(event) {
      let okay = document.forms["exercise-edit"].checkValidity() &&
                 controller.checkExerciseDuration(event) &&
                 controller.checkExerciseEditForm(event);
      if(!okay) {
        M.toast({html: "Bitte fülle alle Felder korrekt aus.", classes: "red accent-3 rounded"});
      } else {
        let phases = Array.from(view.exercisePhases.selectedOptions).map(option => option.value);
        let modifiedExercise = {
          id: view.exerciseId.value,
          name: view.exerciseName.value,
          warmup: phases.includes("warmup"),
          runabc: phases.includes("runabc"),
          mainex: phases.includes("mainex"),
          ending: phases.includes("ending"),
          sticky: view.model.selectedExercise.sticky,
          material: view.exerciseMaterial.value,
          durationmin: parseInt(view.exerciseDurationMin.value, 10),
          durationmax: parseInt(view.exerciseDurationMax.value, 10),
          repeats: view.exerciseReps.value,
          details: view.exerciseDetails.value,
          disciplines: Array.from(view.exerciseDisciplines.selectedOptions).map(option => option.value),
        };
        let request = new Request("db_update.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
          body: (view.model.copying ? "create=" : "update=") + JSON.stringify(modifiedExercise)
        });
        fetch(request)
        .then(response => response.json())
        .then(data => {
          if(data.success) {
            M.toast({html: "Übung erfolgreich gespeichert.", classes: "green accent-3 rounded"});
          } else {
            console.error("Error saving exercise:", data);
            M.toast({html: "Fehler beim Speichern der Übung: " + data.message, classes: "red accent-3 rounded"});
          }
        })
        .then(() => App.loadData("../")) // reload data to ensure the data in the browser is up-to-date
        .then( (result) => {
          view.model.copying = false; // reset the copying flag
          // update the selected exercise
          view.model.selectedExercise = Exercise.getAll().find(exercise => exercise.id === modifiedExercise.id);
          view.fillExerciseList();
          view.updateExerciseForm(); // update the form to reflect the changes 
        })
        .catch(error => {
          console.error("Error saving exercise:", error);
          M.toast({html: "Fehler beim Speichern der Übung.", classes: "red accent-3 rounded"});
        });
      }
    },

    onCopyExercise(event) {
      if (view.model.selectedExercise === undefined) { return; }
      view.model.copying = true; // set the copying flag to true
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
      let confirmDialog = M.Modal.getInstance(document.getElementById("confirm-delete"));
      confirmDialog.close();
      let request = new Request("db_update.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded"},
        body: "delete=" + JSON.stringify(view.model.selectedExercise.id)
      });
      fetch(request)
      .then(response => response.json())
      .then(data => {
        if(data.success) {
          M.toast({html: "Übung erfolgreich gelöscht.", classes: "green accent-3 rounded"});
          // delete the exercise in the model
          App.loadData("../").then( (result) => {  
            view.model.selectedExercise = undefined; // clear the selected exercise
            view.fillExerciseList();
            view.updateExerciseForm();
          });
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
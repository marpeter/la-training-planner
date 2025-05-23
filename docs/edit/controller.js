import { TrainingPlan, Exercises } from "../model.js";

document.addEventListener('DOMContentLoaded', function() {
    TrainingPlan.loadData("../").then( (result) => {
        let uiModel = {
          version: TrainingPlan.version,
          disciplines: TrainingPlan.getAllDisciplines(),
          selectedExercise: undefined, 
          exerciseListFilter: "",
        };
        view.finishUi(uiModel);
        controller.registerEventHandlers();    
    });
 });

const view = {
  model: undefined,
  finishUi(model) {
      this.model = model;
      this.fillExerciseList();
      this.fillDisciplineSelect();
      this.updateExerciseForm();
      this.updateVersion();
  },

  // fill the exercise list with the exercises from the model
  fillExerciseList() {
    let exerciseList = document.getElementById("exercise-list");
    // clear the list, keeping the header
    let children = Array.from(exerciseList.children);
    for (let i = 1; i< children.length; i++) {
      exerciseList.removeChild(children[i]);
    }

    Exercises.forEach( (exercise) => {
      // skip the "Auslaufen" exercise, it is always the last one
      if (!exercise.sticky && exercise.name.startsWith(this.model.exerciseListFilter)) {
        let item = document.createElement("li");
        item.classList.add("collection-item", "left-align");
        let anchor = document.createElement("a");
        anchor.classList.add("red-text");
        anchor.innerHTML = exercise.name; // + " (" + exercise.id + ")";
        anchor.setAttribute("href", "#!");
        anchor.setAttribute("data-id", exercise.id);
        anchor.onclick = () => controller.onExerciseSelected(exercise.id);
        item.appendChild(anchor);
        exerciseList.appendChild(item);
      }
    });
  },

  // fill the discipline select with the disciplines from the model
  fillDisciplineSelect() {
    let disciplineSelect = document.getElementById("exercise-disciplines");
    this.model.disciplines.forEach( (discipline) => {
      let option = document.createElement("option");
      option.value = discipline.id;
      option.innerHTML = discipline.name;
      // option.setAttribute("data-icon",discipline.image);
      disciplineSelect.appendChild(option);
    });
  },

  // update the exerise form with the selected exercise
  updateExerciseForm() {
    let exercise = this.model.selectedExercise;
    let exerciseId = document.getElementById("exercise-id");
    let exerciseName = document.getElementById("exercise-name");
    let exercisePhases = document.getElementById("exercise-phases");
    let exerciseDisciplines = document.getElementById("exercise-disciplines");
    let exerciseMaterial = document.getElementById("exercise-material");
    let exerciseDurationMin = document.getElementById("exercise-duration-min");
    let exerciseDurationMax = document.getElementById("exercise-duration-max");
    let exerciseReps = document.getElementById("exercise-reps");
    let exerciseDetails = document.getElementById("exercise-details");
    if (exercise === undefined) {
      exerciseId.value = "";
      exerciseName.value = "";
      exerciseName.disabled = true;
      exercisePhases.disabled = true;
      exerciseDisciplines.disabled = true;
      exerciseMaterial.value = "";
      exerciseMaterial.disabled = true;
      exerciseDurationMin.value = "";
      exerciseDurationMin.disabled = true;
      exerciseDurationMax.value = "";
      exerciseDurationMax.disabled = true;
      exerciseReps.value = "";
      exerciseReps.disabled = true;
      exerciseDetails.value = "";
      exerciseDetails.disabled = true;
      document.getElementById("save-exercise").classList.add("disabled");
      document.getElementById("copy-exercise").classList.add("disabled");
      document.getElementById("delete-exercise").classList.add("disabled");
    } else {
      exerciseId.value = exercise.id;
      exerciseName.value = exercise.name;
      exerciseName.disabled = false;
      exercisePhases.disabled = false;
      Array.from(exercisePhases.children).forEach( (option) => option.selected = exercise[option.value] );
      exerciseDisciplines.disabled = false;
      Array.from(exerciseDisciplines.children).forEach( (option) => option.selected = exercise.disciplines.includes(option.value) );
      exerciseMaterial.value = exercise.material;
      exerciseMaterial.disabled = false;
      exerciseDurationMin.value = exercise.durationmin;
      exerciseDurationMin.disabled = false;
      exerciseDurationMax.value = exercise.durationmax;
      exerciseDurationMax.disabled = false;
      exerciseReps.value = exercise.repeats;
      exerciseReps.disabled = false;
      exerciseDetails.value = exercise.details;
      exerciseDetails.disabled = false;
      if(this.model.version.supportsEditing) {
        document.getElementById("save-exercise").classList.remove("disabled");
        document.getElementById("copy-exercise").classList.remove("disabled");
        document.getElementById("delete-exercise").classList.remove("disabled");
      }
    }
    M.updateTextFields();
    M.FormSelect.init(document.getElementById("exercise-disciplines"));
    M.FormSelect.init(document.getElementById("exercise-phases"));
  },

  // update the version number in the footer of the page and enable the edit button if the version supports editing
  updateVersion() {
    let versionElement = document.getElementById("version");
    versionElement.innerHTML = this.model.version.number;
  },      
}

const controller = {
    registerEventHandlers() {
      document.getElementById("exercise-filter").addEventListener("input", this.onExcerciseFilterChanged);
      document.getElementById("exercise-duration-min").addEventListener("change", this.checkExerciseDuration);
      document.getElementById("exercise-duration-max").addEventListener("change", this.checkExerciseDuration);
      document.getElementById("exercise-edit").addEventListener("change", this.checkExerciseEditForm);
      document.getElementById("save-exercise").addEventListener("click", this.onSaveExercise);
      // initialize the static select elements
      M.FormSelect.init(document.querySelectorAll('select'));
      // initialize the dynamic select elements
      M.FormSelect.init(document.getElementById("exercise-disciplines"));
    },

    onExcerciseFilterChanged(event) {
      view.model.exerciseListFilter = event.target.value;
      view.fillExerciseList();
    },

    onExerciseSelected(exerciseId) {
      let exercise = Exercises.find( (exercise) => exercise.id==exerciseId );
      view.model.selectedExercise = exercise;
      view.updateExerciseForm();
    },

    checkExerciseDuration(event) {
      let min = document.getElementById("exercise-duration-min");
      let max = document.getElementById("exercise-duration-max");
      console.log("checkExerciseDuration: " + min.value + " - " + max.value);
      if (min.value > max.value) {
        min.setCustomValidity("Die minimale Dauer muss kleiner sein als die maximale Dauer.");
        return false;
      } else {
        min.setCustomValidity("");
        return true;
      }
    },

    checkExerciseEditForm(event) {
      let okay = true;
      let phases = document.getElementById("exercise-phases");
      let helper = document.getElementById("exercise-phases-helper"); 
      if(phases.value === "") {
        helper.classList.add("red-text");
        okay = false;
      } else {
        helper.classList.remove("red-text");
      } 
      let disciplines = document.getElementById("exercise-disciplines");
      helper = document.getElementById("exercise-disciplines-helper");
      if(disciplines.value === "") {
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
      console.log("onSaveExercise: " + okay);
      if(!okay) {
        M.toast({html: "Bitte fülle alle Felder korrekt aus.", classes: "red accent-3 rounded"});
      } else {
        // TODO: save the exercise
      }
    }
}
document.addEventListener('DOMContentLoaded', function() {
  
  let uiModel = {
    disciplines: TrainingPlan.getAllDisciplines(),
    selectedDisciplines: [],
    duration: 70,
  };
  
  view.finishUi(uiModel);
  controller.registerEventHandlers();
 });

const view = {

  model: undefined,

  finishUi(model) {
    this.model = model,
    this.createDisciplineCardList();
    this.updateShowButton();
  },

  createDisciplineCardList() {
    Object.keys(this.model.disciplines).forEach( (discipline) => {
      let disciplineChip = document.createElement("div");
      disciplineChip.classList.add("chip", "red", "lighten-4");
      disciplineChip.id = discipline;
      if( this.model.disciplines[discipline].img.length>0) {
        let disciplineImage = document.createElement("img");
        disciplineImage.classList.add("disziplin");
        disciplineImage.src = this.model.disciplines[discipline].img;
        disciplineChip.appendChild(disciplineImage);
      }
      disciplineChip.appendChild(document.createTextNode(this.model.disciplines[discipline].name));
      disciplineChip.addEventListener("click", controller.onDisciplineSelected);
      document.getElementById("disziplinen").appendChild(disciplineChip);
   } );
  },
  
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
    exercises.forEach( (exercise) => this.addExerciseCard(exercise, phaseDiv));
  },

  addExerciseCard(exercise, toElement) {
    let exerciseDiv = document.createElement("div");
    exerciseDiv.classList.add("col", "s12", "m6");
    let exerciseCard = document.createElement("div");
    exerciseCard.classList.add("card", "red", "lighten-4");
    let exerciseContent = document.createElement("div");
    exerciseContent.classList.add("card-content");
    exerciseContent.innerHTML = (exercise.details.length>0) ?
       `<span class="card-title center activator">${exercise.name}<i class="material-icons right">more_vert</i></span><ul>` :
       `<span class="card-title center">${exercise.name}</span><ul>`;
    exerciseContent.innerHTML +=
      `<li>Material: ${exercise.material}</li>` +
      `<li>Dauer: ${exercise.duration}min</li>`+
      `<li>Wiederholungen: ${exercise.repeat}</li>` +
      `</ul>`;
    exerciseCard.appendChild(exerciseContent);
    if (exercise.details.length>0) {
      let exerciseReveal = document.createElement("div");
      exerciseReveal.classList.add("card-reveal");
      exerciseReveal.innerHTML = '<span class="card-title grey-text text-darken-4">Details<i class="material-icons right">close</i></span>';
      exerciseReveal.innerHTML += '<ul>';
      exercise.details.forEach( (item) => exerciseReveal.innerHTML += `<li>${item}</li>`);
      exerciseReveal.innerHTML += '</ul>'; 
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
    document.getElementById("plan-title").innerHTML = "Plan " + plan.id + ": "
      + plan.disciplines.map((discipline) => discipline.name ).join(" & ") + " (" + plan.duration() + "min)";  
    view.fillCardsForPhase("warmup", plan.warmup);
    view.fillCardsForPhase("mainex", plan.mainex);
    view.fillCardsForPhase("ending", plan.ending);
  },

  onDisciplineSelected(event) {
    let targetElement = (event.target.localName=='img') ?
        event.target.parentElement // click was on image -> discipline element is the parent element
      : event.target;
    let index = view.model.selectedDisciplines.indexOf(view.model.disciplines[targetElement.id]);
    if (index>=0) {
      // discipline was de-selected -> remove it from the list of selected disciplines
      this.model.selectedDisciplines.splice(index,1);
      targetElement.classList.remove("lighten-2");
      targetElement.classList.add("lighten-4");
    } else {
      targetElement.classList.remove("lighten-4");
      targetElement.classList.add("lighten-2");
      view.model.selectedDisciplines.push(view.model.disciplines[targetElement.id]);
    }
    view.updateShowButton();
  },

  onDurationChanged(event) {
    view.model.duration = event.target.value;
  }
};

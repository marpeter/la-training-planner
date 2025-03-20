let selectedDisciplines = [];
let duration = 60;

document.addEventListener('DOMContentLoaded', function() {
  createDisciplineCardList();
  updateShowButton(); 
  document.getElementById("showBtn").addEventListener("click", onCreatePlanButtonPressed);
  document.durationForm.duration.forEach( (radio) => radio.addEventListener("change",onDurationChanged));
 });

// Event handler
function onCreatePlanButtonPressed() {
  let plan = generatePlan();
  if(!plan) {
    alert("Ich konnte keinen Plan finden / erzeugen :-(");
    return;
  }
  document.getElementById("plan-title").innerHTML = "Plan " + plan.id + ": "
    + plan.disciplines.map((discipline) => discipline.name ).join(" & ") + " (" + plan.duration + "min)";  
  fillCardsForPhase("warmup", plan.warmup);
  fillCardsForPhase("mainex", plan.mainex);
  fillCardsForPhase("ending", plan.ending);
}

function onDisciplineSelected(event) {
  let targetElement = (event.target.localName=='img') ?
      event.target.parentElement // click was on image -> discipline element is the parent element
    : event.target;
  let index = selectedDisciplines.indexOf(Disciplines[targetElement.id]);
  if (index>=0) {
    // discipline was de-selected -> remove it from the list of selected disciplines
    selectedDisciplines.splice(index,1);
    targetElement.classList.remove("lighten-2");
    targetElement.classList.add("lighten-4");
  } else {
    targetElement.classList.remove("lighten-4");
    targetElement.classList.add("lighten-2");
    selectedDisciplines.push(Disciplines[targetElement.id]);
  }
  updateShowButton();
}

function onDurationChanged(event) {
  duration = event.target.value;
}
// helper functions for UI

function createDisciplineCardList() {
  Object.keys(Disciplines).forEach( (discipline) => {
     let disciplineChip = document.createElement("div");
     disciplineChip.classList.add("chip", "red", "lighten-4");
     disciplineChip.id = discipline;
     if( Disciplines[discipline].img.length>0) {
       let disciplineImage = document.createElement("img");
       disciplineImage.classList.add("disziplin");
       disciplineImage.src = Disciplines[discipline].img;
       disciplineChip.appendChild(disciplineImage);
     }
     disciplineChip.appendChild(document.createTextNode(Disciplines[discipline].name));
     disciplineChip.addEventListener("click", onDisciplineSelected);
     document.getElementById("disziplinen").appendChild(disciplineChip);
  } );
}

function updateShowButton() {
  let showBtn = document.getElementById("showBtn");
  if(selectedDisciplines.length>0) {
    showBtn.classList.remove("disabled");
  } else {
    showBtn.classList.add("disabled");
  }
}

function fillCardsForPhase(phaseName, exercises) {
  let phaseDiv = document.getElementById(phaseName);
  // clear the current phase content, add cards for the phase exercises
  while(phaseDiv.firstChild) { phaseDiv.removeChild(phaseDiv.firstChild);};
  exercises.forEach( (exercise) => addExerciseCard(exercise, phaseDiv));
}

function addExerciseCard(exercise, toElement) {
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

// helper functions - logic

function generatePlan() {

  let plans = (selectedDisciplines.length==0) ? TrainingPlans :
    TrainingPlans.filter( (plan) => {
        return selectedDisciplines.length == 
          selectedDisciplines.filter( (selected) => plan.disciplines.includes(selected)).length;
      });
  if(plans.length==0) {
    return null;
  } else {
    let plan = structuredClone(plans[Math.floor(Math.random() * plans.length)]);
    plan.ending.push(Exercises.Auslaufen);
    
    determinePlanDuration(plan);
    
    return plan;
  }
}

function determinePlanDuration(plan) {
  plan.duration = 0;
  plan.warmup.forEach( (exercise) =>  plan.duration += parseInt(exercise.duration) );
  plan.mainex.forEach( (exercise) =>  plan.duration += parseInt(exercise.duration) );
  plan.ending.forEach( (exercise) =>  plan.duration += parseInt(exercise.duration) );  
}
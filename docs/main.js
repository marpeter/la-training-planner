let selectedDisciplines = [];
let availablePlans = [];
let planOffset = 0;

function selectDiscipline(event) {
  let index = selectedDisciplines.indexOf(event.target.id);
  if (index>=0) {
    selectedDisciplines.splice(index,1);
    event.target.parentElement.classList.remove("lighten-2");
    event.target.parentElement.classList.add("lighten-4");
  } else {
    event.target.parentElement.classList.remove("lighten-4");
    event.target.parentElement.classList.add("lighten-2");
    selectedDisciplines.push(event.target.id);
  }

  determineAvailablePlans();
  updateShowButtonText();
}

function determineAvailablePlans() {
  if(selectedDisciplines.length>0) {
    availablePlans = TrainingsPlaene.filter(
      (plan) => {return selectedDisciplines.filter( (selected) => plan.disciplines.includes(selected)).length==selectedDisciplines.length;}
    );
  } else {
    availablePlans = TrainingsPlaene;
  }
}

function updateShowButtonText() {
  let showBtn = document.getElementById("showBtn");
  if(availablePlans.length>0) {
    showBtn.innerHTML = "Einen der " + availablePlans.length + " Trainingspl&auml;ne anzeigen";
    showBtn.classList.remove("disabled");
  } else {
    showBtn.innerHTML = "Kein passender Trainingsplan vorhanden";
    showBtn.classList.add("disabled");
  }
}

function updateSelectedPlans() {
  planOffset=0;
  showSelectedPlan();
}
function showSelectedPlan() {
  if(availablePlans.length==0) {
    return;
  }

  plan = availablePlans[planOffset];
  
  document.getElementById("plan-title").innerHTML = "Plan " + plan.id + ": " + plan.disciplines.join(" & ");
  
  // clear the current warmup content, add cards for the warmup methods
  let warmupDiv = document.getElementById("warmup");
  while(warmupDiv.firstChild) { warmupDiv.removeChild(warmupDiv.firstChild);};
  plan.warmup.forEach( (exercise) => addExerciseCard(exercise, warmupDiv));

  // clear the current main exercises content, add cards for the main methods
  let mainexDiv = document.getElementById("mainex");
  while(mainexDiv.firstChild) { mainexDiv.removeChild(mainexDiv.firstChild);};
  plan.mainex.forEach( (exercise) => addExerciseCard(exercise, mainexDiv));

  // clear the current main exercises content, add cards for the main methods
  let endingDiv = document.getElementById("ending");
  while(endingDiv.firstChild) { endingDiv.removeChild(endingDiv.firstChild);};
  plan.ending.forEach( (exercise) => addExerciseCard(exercise, endingDiv));

  // update the "previous" and "next" plan buttons
  if(planOffset>0) {
    document.getElementById("prevBtn").classList.remove("disabled");
  } else {
    document.getElementById("prevBtn").classList.add("disabled");
  }
  if(planOffset<availablePlans.length-1) {
    document.getElementById("nextBtn").classList.remove("disabled");
  } else {
    document.getElementById("nextBtn").classList.add("disabled");
  }
}

function showPreviousPlan() {
  if(planOffset>0) {
    planOffset--;
  }
  showSelectedPlan();
}

function showNextPlan() {
  if(planOffset<availablePlans.length-1) {
    planOffset++;
  }
  showSelectedPlan();
}

function addExerciseCard(exercise, toElement) {
  let exerciseDiv = document.createElement("div");
  exerciseDiv.classList.add("col", "s12", "m6");
  let exerciseCard = document.createElement("div");
  exerciseCard.classList.add("card", "red", "lighten-4");
  let exerciseContent = document.createElement("div");
  exerciseContent.classList.add("card-content", "center");
  if (exercise.details.length>0) {
    exerciseContent.innerHTML = `<span class="card-title activator">${exercise.name}<i class="material-icons right">more_vert</i></span><ul>`;
  } else {
    exerciseContent.innerHTML = `<span class="card-title">${exercise.name}</span><ul>`
  }
  exerciseContent.innerHTML +=
    `<li>Material: ${exercise.material}</li>` +
    `<li>Dauer: ${exercise.duration}</li>`+
    `<li>Wiederholungen: ${exercise.repeat}</li>` +
    `</ul>`;
  if (exercise.details.length>0) {
    let exerciseReveal = document.createElement("div");
    exerciseReveal.classList.add("card-reveal");
    exerciseReveal.innerHTML = '<span class="card-title grey-text text-darken-4">Details<i class="material-icons right">close</i></span>';
    exerciseReveal.innerHTML += '<ul>';
    addListItems(exerciseReveal, exercise.details);
    exerciseReveal.innerHTML += '</ul>'; 
    exerciseCard.appendChild(exerciseContent);
    exerciseCard.appendChild(exerciseReveal);
  } else {
    exerciseCard.appendChild(exerciseContent);
  }
  exerciseDiv.appendChild(exerciseCard);
  toElement.appendChild(exerciseDiv);
}

function addListItems(element, list) {
  list.forEach( (item) => {
    element.innerHTML += `<li>${item}</li>`;
  });
}

function initializeCardList() {
  let disziplinen = document.getElementById("disziplinen");
  Disziplinen.forEach( (discipline) => {
     let disziplin = document.createElement("div");
     disziplin.classList.add("col", "s4", "m3", "l2");
     let disziplinCard = document.createElement("div");
     disziplinCard.classList.add("card", "red", "lighten-4");
     let disziplinCardContent = document.createElement("div");
     disziplinCardContent.classList.add("card-content", "center");
     disziplinCardContent.id = discipline.name;
     if( discipline.img.length>0) {
       let disziplinCardImage = document.createElement("img");
       disziplinCardImage.classList.add("disziplin");
       disziplinCardImage.src = discipline.img;
       disziplinCardContent.appendChild(disziplinCardImage);
     }
     disziplinCardContent.appendChild(document.createTextNode(discipline.name));
     disziplinCard.appendChild(disziplinCardContent);
     disziplinCard.addEventListener("click", selectDiscipline);
     disziplin.appendChild(disziplinCard);
     disziplinen.appendChild(disziplin);
  } );
  let showBtn = document.getElementById("showBtn");
  showBtn.addEventListener("click", updateSelectedPlans);
  prevBtn.addEventListener("click", showPreviousPlan);
  nextBtn.addEventListener("click", showNextPlan)

}

document.addEventListener('DOMContentLoaded', function() {
  initializeCardList();
  determineAvailablePlans();
  updateShowButtonText(); 
 });
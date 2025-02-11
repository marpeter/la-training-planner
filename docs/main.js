const Disziplinen = [
    { name: "Ausdauer", img: ""},
    { name: "Hochsprung", img: "assets/Hochsprung.png"},
    { name: "Koordination", img: "assets/Koordination.png"},
    { name: "Schnelligkeit", img: "assets/Lauf.png"},
    { name: "Schnellaufen", img: "assets/Lauf.png"},
    { name: "Stabweitsprung", img: "assets/Weitsprung.png"},
    { name: "Staffellauf", img: "assets/Lauf.png"},
    { name: "Staffellauf H\xFCrde", img: "assets/Huerdenlauf.png"},
    { name: "\xDCberlaufen", img: "assets/Hochsprung.png"},
    { name: "Weitsprung (mit Grube)", img: "assets/Weitsprung.png"},
    { name: "Weitsprung (ohne Grube)", img: "assets/Weitsprung.png"},
    { name: "Wurf", img: "assets/Wurf.png"},
];

const TrainingsPlaene = [
  { id: 1, disciplines: ["Ausdauer", "Weitsprung (mit Grube)"],
    warmup: [
      { name: "Seilspringen und Runden laufen",
        material: "Sprungseile", duration: "5", repeat: "2-3 Runden",
        details: [] },
      { name: "Lauf ABC",
        material: "H\xFCtchen", duration: "10", repeat: " - ",
        details: ["Froschspr\xFCnge", "Hopserlauf", "Anversen", "Knieheberlauf", "Steps", "Sprungläufe", "Steigerung"]
      }],
    mainex: [
      { name: "Seilspringen langes Seil",
        material: "Langes Seil", duration: "10", repeat: "",
        details: [] },
      { name: "Seilspringen",
        material: "Sprungseile", duration: "10", repeat: "",
        details: []},
      { name: "Weitsprung in Sprunggrube mit Bananenkisten",
        material: "Bananenkiste, Besen, Rechen, Reifen", duration: "10", repeat: "",
        details: ["Sprünge die Treppenstufen hoch --> jedes Kind nach dem Sprung auf dem Rückweg rechts",
                  "Sprünge 5x mal rein und raus aus dem Reifen", "Aufteilen nach Stärke"]},
      { name: "Weitsprung in Sprunggrube ohne Bananenkisten",
        material: "Bananenkiste, Besen, Rechen, Reifen", duration: "10", repeat: "",
        details: ["Sprünge die Treppenstufen hoch --> jedes Kind nach dem Sprung auf dem Rückweg rechts",
                  "Sprünge 5x mal rein und raus aus dem Reifen", "Aufteilen nach Stärke"]
      }],
    ending: [
      { name: "Anhänger - Abhänger Staffel",
        material: "Seil", duration: "10", repeat: "1 mal",
        details: [] },
      { name: "Auslaufen",
        material: "Sprungseile", duration: "5", repeat: "2 Runden",
        details: []}] 
  },
  { id: 2, disciplines: ["Ausdauer", "Weitsprung (mit Grube)"] },
  { id: 3, disciplines: ["Ausdauer", "Weitsprung (ohne Grube)", "Wurf"] },
  { id: 4, disciplines: ["Ausdauer", "Weitsprung (ohne Grube)", "Wurf"] },
  { id: 5, disciplines: ["Ausdauer", "Weitsprung (ohne Grube)", "Wurf"] },
  { id: 6, disciplines: ["Ausdauer", "Weitsprung (ohne Grube)", "Wurf"] },
  { id: 7, disciplines: ["Ausdauer", "Wurf"] },
  { id: 8, disciplines: ["Ausdauer", "Hochsprung"] },
];

let selectedDisciplines = [];
let availablePlans = [];

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

function showSelectedPlan() {
  if(availablePlans.length==0) {
    return;
  }

  plan = availablePlans[0]; // TODO: make random if availablePlans.length > 1

  document.getElementById("plan-title").innerHTML = "Plan " + plan.id + ": " + plan.disciplines.join(" & ");
  
  let warmupDiv = document.getElementById("warmup");
  // clear the current content
  while(warmupDiv.firstChild) { warmupDiv.removeChild(warmupDiv.firstChild);};
  // add cards for the warmup methods
  plan.warmup.forEach( (exercise) => addExerciseCard(exercise, warmupDiv));

  let mainexDiv = document.getElementById("mainex");
  // clear the current content
  while(mainexDiv.firstChild) { mainexDiv.removeChild(mainexDiv.firstChild);};
  // add cards for the main methods
  plan.mainex.forEach( (exercise) => addExerciseCard(exercise, mainexDiv));

  let endingDiv = document.getElementById("ending");
  // clear the current content
  while(endingDiv.firstChild) { endingDiv.removeChild(endingDiv.firstChild);};
  // add cards for the ending methods
  plan.ending.forEach( (exercise) => addExerciseCard(exercise, endingDiv));
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
  showBtn.addEventListener("click", showSelectedPlan);

}

document.addEventListener('DOMContentLoaded', function() {
  initializeCardList();
  determineAvailablePlans();
  updateShowButtonText(); 
 });
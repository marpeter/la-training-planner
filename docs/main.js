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
  { id: 1, disciplines: ["Ausdauer", "Weitsprung (mit Grube)"]},
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
}

document.addEventListener('DOMContentLoaded', function() {
  initializeCardList();
  determineAvailablePlans();
  updateShowButtonText(); 
 });
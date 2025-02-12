const Disziplinen = [
    { name: "Ausdauer", img: ""},
    { name: "Hochsprung", img: "assets/Hochsprung.png"},
    { name: "Koordination", img: "assets/Koordination.png"},
    { name: "Schnelligkeit", img: "assets/Lauf.png"},
    { name: "Schnellaufen", img: "assets/Lauf.png"},
    { name: "Stabweitsprung", img: "assets/Weitsprung.png"},
    { name: "Staffellauf", img: "assets/Lauf.png"},
    { name: "Staffellauf Hürde", img: "assets/Huerdenlauf.png"},
    { name: "Überlaufen", img: "assets/Hochsprung.png"},
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
        material: "Hütchen", duration: "10", repeat: " - ",
        details: ["Froschsprünge", "Hopserlauf", "Anversen", "Knieheberlauf", "Steps", "Sprungläufe", "Steigerung"]
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
        material: "", duration: "5", repeat: "2 Runden",
        details: []}] 
  },
  { id: 2, disciplines: ["Ausdauer", "Weitsprung (mit Grube)"],
    warmup: [
      { name: "Seilspringen und Runden laufen",
        material: "Sprungseile", duration: "5", repeat: "2-3 Runden",
        details: [] },
      { name: "Lauf ABC",
        material: "Hütchen", duration: "10", repeat: " - ",
        details: ["Froschsprünge", "Hopserlauf", "Anversen", "Knieheberlauf", "Steps", "Sprungläufe", "Steigerung"]
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
      { name: "6 Tage Rennen",
        material: "Hütchen", duration: "10", repeat: "1 mal",
        details: [] },
      { name: "Auslaufen",
        material: "", duration: "5", repeat: "2 Runden",
        details: []}]
  },
  { id: 3, disciplines: ["Ausdauer", "Weitsprung (ohne Grube)", "Wurf"],
    warmup: [
      { name: "Überholstaffel",
        material: "", duration: "5", repeat: "2-3 Runden",
        details: [] },
      { name: "Lauf ABC",
        material: "Hütchen", duration: "10", repeat: " - ",
        details: ["Hopserlauf", "Seitgalopp", "Seitkreuzschritte", "Schlagläufe", "Rückwärtslauf", "Steigerung"]
      }],
    mainex: [
      { name: "Springen am Reifen und Koordinationsleiter",
        material: "Reifen, Koordinationsleiter", duration: "10", repeat: "",
        details: [] },
      { name: "Werfen ohne Anlauf",
        material: "Bälle, Maßband, Hütchen", duration: "15", repeat: "",
        details: ["3 Liegestützen nach jedem Wurf"]},
      { name: "Werfen mit Anlauf",
        material: "Bälle, Maßband, Hütchen", duration: "15", repeat: "",
        details: ["3 Liegestützen nach jedem Wurf"]
      }],
    ending: [
      { name: "Zeitschätzlauf",
        material: "Hütchen", duration: "10", repeat: "1 mal",
        details: [] },
      { name: "Auslaufen",
        material: "", duration: "5", repeat: "2 Runden",
        details: []}]
  },
  { id: 4, disciplines: ["Ausdauer", "Weitsprung (ohne Grube)", "Wurf"],
    warmup: [
      { name: "Transportlauf",
        material: "Tennisbälle, Bananenkisten", duration: "5", repeat: "2-3 Runden",
        details: [] },
      { name: "Lauf ABC",
        material: "Hütchen", duration: "10", repeat: " - ",
        details: ["Hopserlauf", "Seitgalopp", "Seitkreuzschritte", "Schlagläufe", "Rückwärtslauf", "Steigerung"]
      }],
    mainex: [
      { name: "Steigesprünge auf der Bahn",
        material: "Bananenkisten, Hütchen", duration: "10", repeat: "",
        details: [] },
      { name: "Werfen ohne Anlauf",
        material: "Bälle, Maßband, Hütchen", duration: "15", repeat: "",
        details: ["3 Situps nach jedem Wurf"]},
      { name: "Werfen mit Anlauf",
        material: "Bälle, Maßband, Hütchen", duration: "15", repeat: "",
        details: ["3 Situps nach jedem Wurf"]
      }],
    ending: [
      { name: "Biathlon",
        material: "Hütchen", duration: "10", repeat: "1 mal",
        details: [] },
      { name: "Auslaufen",
        material: "", duration: "5", repeat: "2 Runden",
        details: []}]
  },
  { id: 5, disciplines: ["Ausdauer", "Weitsprung (ohne Grube)", "Wurf"],
    warmup: [
      { name: "Autofahren mit Gängen",
        material: "", duration: "5", repeat: "2-3 Runden",
        details: ["Selbst mitlaufen"] },
      { name: "Lauf ABC",
        material: "Hütchen", duration: "10", repeat: " - ",
        details: ["Hopserlauf", "Anversen", "Knieheberlauf", "Schlagläufe",
                  "Auf einem Bein hüpfen und links 1 Kontakt, rechts 2 Kontakte", "Steps", "Steigerung"]
      }],
    mainex: [
      { name: "Über Bloxx laufen",
        material: "Bloxx, Hütchen", duration: "10", repeat: "",
        details: [] },
      { name: "Werfen ohne Anlauf",
        material: "Bälle u.A., Maßband, Hütchen", duration: "15", repeat: "",
        details: []},
      { name: "Werfen mit Anlauf",
        material: "Bälle u.A., Maßband, Hütchen", duration: "15", repeat: "",
        details: []
      }],
    ending: [
      { name: "Transportlauf",
        material: "Hütchen, etwas zu transportieren", duration: "10", repeat: "1 mal",
        details: [] },
      { name: "Auslaufen",
        material: "", duration: "5", repeat: "2 Runden",
        details: []}]
  },
  { id: 6, disciplines: ["Ausdauer", "Weitsprung (ohne Grube)", "Wurf"],
    warmup: [
      { name: "Überholstaffel",
        material: "", duration: "5", repeat: "2-3 Runden",
        details: ["Selbst mitlaufen bei schwacher Gruppe"] },
      { name: "Lauf ABC",
        material: "Hütchen", duration: "10", repeat: " - ",
        details: ["Hopserlauf", "Anversen", "Knieheberlauf", "Schlagläufe",
                  "Auf einem Bein hüpfen und links 1 Kontakt, rechts 2 Kontakte", "Steps", "Steigerung"]
      }],
    mainex: [
      { name: "Durch Reifen hüpfen",
        material: "Reifen", duration: "10", repeat: "",
        details: [] },
      { name: "Werfen ohne Anlauf, ggf. auch stoßen",
        material: "Bälle, Maßband, Hütchen, Medibälle", duration: "15", repeat: "",
        details: ["3 Liegestützen nach jedem Wurf"]},
      { name: "Werfen mit Anlauf, ggf. auch stoßen",
        material: "Bälle, Maßband, Hütchen, Medibälle", duration: "15", repeat: "",
        details: ["3 Liegestützen nach jedem Wurf"]
      }],
    ending: [
      { name: "Transportlauf",
        material: "Hütchen, etwas zu transportieren", duration: "10", repeat: "1 mal",
        details: [] },
      { name: "Auslaufen",
        material: "", duration: "5", repeat: "2 Runden",
        details: []}]
  },
  { id: 7, disciplines: ["Ausdauer", "Wurf"],
    warmup: [
      { name: "Bälle prellen",
        material: "Bälle", duration: "5", repeat: "2-3 Runden",
        details: [] },
      { name: "Lauf ABC",
        material: "Hütchen", duration: "10", repeat: " - ",
        details: ["Hopserlauf", "Seitgalopp", "Seitkreuzschritte", "Schlagläufe", "Rückwärtslauf", "Steigerung"]
      }],
    mainex: [
      { name: "Pendelstaffel",
        material: "Bloxx, Hütchen", duration: "10", repeat: "",
        details: [] },
      { name: "Sau durchs Dorf",
        material: "Medibälle, Tennisbälle", duration: "10", repeat: "",
        details: []},
      { name: "Werfen ohne Anlauf",
        material: "Alle werfbaren Gegenstände, Hütchen, Maßband", duration: "10", repeat: "",
        details: ["Medibälle, die durch die Beine wie eine 8 geführt werden müssen (5 Durchgänge)"]},
      { name: "Werfen mit Anlauf",
        material: "Alle werfbaren Gegenstände, Hütchen, Maßband", duration: "10", repeat: "",
        details: ["Medibälle, die durch die Beine wie eine 8 geführt werden müssen (5 Durchgänge)"]
      }],
    ending: [
      { name: "Biathlon",
        material: "", duration: "10", repeat: "1 mal",
        details: [] },
      { name: "Auslaufen",
        material: "", duration: "5", repeat: "2 Runden",
        details: []}]    
   },
  { id: 8, disciplines: ["Ausdauer", "Hochsprung"],
    warmup: [
      { name: "Formen ablaufen auf Rasen",
        material: "Hütchen", duration: "10", repeat: "2-3 Runden",
        details: [] },
      { name: "Lauf ABC",
        material: "Hütchen", duration: "10", repeat: " - ",
        details: ["Hopserlauf", "Anversen", "Knieheberlauf", "Steps", "Sprungläufe", "Steigerung"]
      }],
    mainex: [
      { name: "Läufer gegen Werfer im Kreis",
        material: "Ball, Hütchen", duration: "10", repeat: "",
        details: [] },
      { name: "Band schräg knoten und hochspringen lassen",
        material: "Band", duration: "10", repeat: "",
        details: []},
      { name: "8er in Kurven laufen",
        material: "Hütchen", duration: "10", repeat: "",
        details: ["5 Sprünge hoch auf Sitzsteine"]},
      { name: "Hochsprung an Anlage mit Hütchen als Absperrung",
        material: "Hütchen", duration: "10", repeat: "",
        details: ["5 Sprünge hoch auf Sitzsteine"]
      }],
    ending: [
      { name: "Klammerlauf",
        material: "Klammern", duration: "10", repeat: "1 mal",
        details: [] },
      { name: "Auslaufen",
        material: "", duration: "5", repeat: "2 Runden",
        details: []}]    
   },
];

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
  showBtn.addEventListener("click", showSelectedPlan);
  prevBtn.addEventListener("click", showPreviousPlan);
  nextBtn.addEventListener("click", showNextPlan)

}

document.addEventListener('DOMContentLoaded', function() {
  initializeCardList();
  determineAvailablePlans();
  updateShowButtonText(); 
 });
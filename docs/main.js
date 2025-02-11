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

document.addEventListener('DOMContentLoaded', function() {
   let disziplinen = document.getElementById("disziplinen");
   Disziplinen.forEach( (discipline) => {
      let disziplin = document.createElement("div");
      disziplin.classList.add("col", "s4", "m3", "l2");
      let disziplinCard = document.createElement("div");
      disziplinCard.classList.add("card");
      let disziplinCardContent = document.createElement("div");
      disziplinCardContent.classList.add("card-content", "center");
      if( discipline.img.length>0) {
        let disziplinCardImage = document.createElement("img");
        disziplinCardImage.classList.add("disziplin");
        disziplinCardImage.src = discipline.img;
        disziplinCardContent.appendChild(disziplinCardImage);
      }
      disziplinCardContent.appendChild(document.createTextNode(discipline.name));
      disziplinCard.appendChild(disziplinCardContent);
      disziplin.appendChild(disziplinCard);
      disziplinen.appendChild(disziplin);
   } );

 });
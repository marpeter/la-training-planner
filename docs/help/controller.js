import { App } from "../model.js";

document.addEventListener('DOMContentLoaded', function() {
  App.getVersion("../").then( (version) => {
    document.getElementById("version").innerHTML = version.number;
    if(version.supportsEditing) {
      let editBtn = document.getElementById("editBtn");
      editBtn.classList.remove("disabled");
    }
  });
});
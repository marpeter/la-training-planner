import { App } from "../model.js";

document.addEventListener('DOMContentLoaded', () => {
  App.getVersion("../").then( (version) => {
    document.getElementById("version").innerHTML = version.number;
    if(version.supportsEditing) {
      document.getElementById("editBtn").classList.remove("disabled");
    }
  });
});
import { App } from "../model.js";
import { updateLogInOutButton } from "../common-controller.js";

document.addEventListener('DOMContentLoaded', () => {
  App.getVersion("../").then( (version) => {
    document.getElementById("version").innerHTML = version.number;
    if(version.supportsEditing) {
      document.getElementById("editBtn").classList.remove("disabled");
    } else {
      document.getElementById("editBtn").classList.add("disabled");
    }
    updateLogInOutButton('loginBtn', version, '..');
  });
});
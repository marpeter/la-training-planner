import { App } from "../model.js";

document.addEventListener('DOMContentLoaded', () => {
  App.getVersion("../").then( (version) => {
    document.getElementById("version").innerHTML = version.number;
    if(version.supportsEditing) {
      document.getElementById("editBtn").classList.remove("disabled");
      // So far there is no login screen ...
      document.getElementById("loginBtn").classList.add("disabled");
    } else {
      document.getElementById("loginBtn").classList.add("disabled");
      document.getElementById("editBtn").classList.add("disabled");
    }
  });
});
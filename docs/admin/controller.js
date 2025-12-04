import { App } from "../model.js";

document.addEventListener('DOMContentLoaded', () => {
  App.getVersion("../").then( (version) => {
    if( version.supportsUpload) {
      window.location = "admin.php";
      document.getElementById("loginBtn").classList.add("disabled");
    } else {
      document.getElementById("version").innerHTML = version.number;
      document.getElementById("editBtn").classList.add("disabled");
    }
  });
});
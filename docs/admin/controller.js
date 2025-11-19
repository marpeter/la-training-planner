import { App } from "../model.js";

document.addEventListener('DOMContentLoaded', () => {
  App.getVersion("../").then( (version) => {
    if( version.supportsUpload) {
      window.location = "admin.php";
    } else {
      document.getElementById("version").innerHTML = version.number;
    }
  });
});
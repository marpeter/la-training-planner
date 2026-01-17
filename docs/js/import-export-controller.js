import { App } from "./model.js";

document.addEventListener('DOMContentLoaded', () => {
  App.getVersion().then( (version) => {
    if( version.withDB) {
      window.location = "./admin/admin.php";
    } else {
      document.getElementById("version").innerHTML = version.number;
      document.getElementById("editBtn").classList.add("disabled");
    }
  });
});
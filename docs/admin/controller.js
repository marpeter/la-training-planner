import { App } from "../model.js";

document.addEventListener('DOMContentLoaded', function() {
  App.getVersion("../").then( (version) => {
    if( version.supportsUpload) {
      window.location = "db_upload.php";
    } else {
      document.getElementById("version").innerHTML = version.number;
    }
  });
});
import { dbVersion } from "../data/db.js";

document.addEventListener('DOMContentLoaded', function() {
  dbVersion("../").then( (result) => {
    if( result.supportsDownload) {
      window.location = "db_upload.php";
    } else {
      let versionElement = document.getElementById("version");
       versionElement.innerHTML = result.number;
    }
  });
});
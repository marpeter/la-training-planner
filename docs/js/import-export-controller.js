import { initPage } from "./common.js";

initPage(continueInitPage);

function continueInitPage(user, version) {
  if( version.withDB) {
    window.location = "./import-export.php";
  }
}
import { initPage } from "./common.js";

initPage(continueInitPage);

function continueInitPage(version) {
  if( version.withDB) {
    window.location = "./admin/admin.php";
  }
}
import { App } from "./model.js";

function updateLogInOutButton(version) {

    let logInOutButton = document.getElementById("loginBtn");
    let loginIcon = logInOutButton.firstChild;

    if( version.username ) {
      logInOutButton.classList.remove("disabled");
      logInOutButton.href = "./login/logout.php?url=" + window.location.href;
      loginIcon.innerHTML = 'logout';
    } else {
      if( !version.withDB ) logInOutButton.classList.add("disabled");
      logInOutButton.href = "./login/";
      loginIcon.innerHTML = 'login';   
    }
}

function updateVersionNumber(version) {
    document.getElementById("version").innerHTML = version.number;
}

function updateEditMenuItem(version) {
    if(version.supportsEditing) {
      document.getElementById("menuItemEdit").classList.remove("disabled");
    } else {
      document.getElementById("menuItemEdit").classList.add("disabled");
    }
}

function initPage(continueWith) {
  document.addEventListener('DOMContentLoaded', () => {
  App.getVersion().then( (version) => {
    if( version.withBackend && !version.withDB ) {
      alert("The application is not yet set up. You will be redirected to the setup page.");
      window.location = "./admin/setup.php";
    } else {
        if( continueWith ) continueWith(version);
        updateLogInOutButton(version);
        updateVersionNumber(version);
        updateEditMenuItem(version);
    }});
  });
}

export { initPage }
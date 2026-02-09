import { Backend } from "./backend.js";

function initPage(continueWith) {
  document.addEventListener('DOMContentLoaded', () => {
  Backend.bind()
    .then( () => Backend.getVersionAndUserInfo() ) 
    .then( ({ version, user }) => {
      if( version.withBackend && !version.withDB ) {
        window.location = "./setup.php";
      } else {
          if( continueWith ) continueWith(user, version);
          updateLogInOutButton(version, user);
          updateVersionNumber(version);
          updateEditMenuItem(user);
      }
    });
  });
}

function updateLogInOutButton(version, user) {

    let logInOutButton = document.getElementById("loginBtn");
    let loginIcon = logInOutButton.firstChild;

    if( user.name ) {
      logInOutButton.classList.remove("disabled");
      logInOutButton.href = "./index.php/user/logout?url=" + window.location.href;
      loginIcon.innerHTML = 'logout';
    } else {
      if( !version.withDB ) logInOutButton.classList.add("disabled");
      logInOutButton.href = "./login.html";
      loginIcon.innerHTML = 'login';   
    }
}

function updateVersionNumber(version) {
    document.getElementById("version").innerHTML = version.numberText;
}

function updateEditMenuItem(user) {
    if(user.canEdit) {
      document.getElementById("menuItemEdit").classList.remove("disabled");
    } else {
      document.getElementById("menuItemEdit").classList.add("disabled");
    }
}

export { initPage }
function updateLogInOutButton(elementId, version, pathToAppRoot) {

    let logInOutButton = document.getElementById("loginBtn");
    let loginIcon = logInOutButton.firstChild;

    if( version.username ) {
      logInOutButton.classList.remove("disabled");
      logInOutButton.href = pathToAppRoot + "/login/logout.php?url=" + window.location.href;
      loginIcon.innerHTML = 'logout';
    } else {
      if( !version.withDB ) logInOutButton.classList.add("disabled");
      logInOutButton.href = pathToAppRoot + "/login/";
      loginIcon.innerHTML = 'login';   
    }

}

export { updateLogInOutButton }
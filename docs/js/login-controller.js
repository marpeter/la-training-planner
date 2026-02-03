import { initPage } from "./common.js";

initPage(continueInitPage);

function continueInitPage(user, version) {
  view.finishUi({
    user: user,
    version: version });
  controller.registerEventHandlers();    
}

const view = {
  user: undefined,
  version: undefined,
  loginButton: undefined,
  changePwdButton: undefined,
  userNameInput: undefined,
  passwordInput: undefined,
  newPasswordInput: undefined,
  passwordRepeatInput: undefined,

  finishUi(model) {
    this.user = model.user;
    this.version = model.version;
    this.loginButton = document.getElementById("login");
    this.changePwdButton = document.getElementById("changePwdBtn");
    this.userNameInput = document.getElementById("username");
    this.passwordInput = document.getElementById("password");
    this.newPasswordInput = document.getElementById("newpassword");
    this.passwordRepeatInput = document.getElementById("newpasswordrepeat");

    this.updateFields();
  },

  updateFields() {
    let userNameField = document.getElementById("username-field");
    let newPasswordField = document.getElementById("password-new");
    let newPasswordRepeat = document.getElementById("password-new-repeat");
    let welcomeArea = document.getElementById("welcome");
    
    if(this.user.loggedIn) {
      userNameField.classList.add("hide");
      this.loginButton.classList.add("hide");

      newPasswordField.classList.remove("hide");
      newPasswordRepeat.classList.remove("hide");
      this.changePwdButton.classList.remove("hide");
      welcomeArea.classList.remove("hide");
      document.getElementById("welcome-user").innerHTML = this.user.name;
      document.getElementById("welcome-role").innerHTML = this.user.role;
      document.getElementById("logoutBtn").href = "./index.php/user/logout?url=" + window.location.href;
      let manageUserButton = document.getElementById("manage-users");
      if(this.user.canEdit) {
        manageUserButton.classList.remove("hide");
      } else {
        manageUserButton.classList.add("hide");
      }
    } else {
      userNameField.classList.remove("hide");
      if(this.version.withDB) {
        this.loginButton.classList.remove("hide");
      } else {
        this.loginButton.classList.add("hide");
      }

      newPasswordField.classList.add("hide");
      newPasswordRepeat.classList.add("hide");  
      this.changePwdButton.classList.add("hide");   
      welcomeArea.classList.add("hide");

      this.userNameInput.disabled = !this.version.withDB;
    }
  }
}

const controller = {
  registerEventHandlers() {
    view.loginButton.onclick = this.doLogin;
    view.changePwdButton.onclick = this.changePassword;
  },

  doLogin(){
    let request = new Request('./index.php/user/login',{
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({
        username: view.userNameInput.value,
        password : view.passwordInput.value}
    )});
    fetch(request)
      .then( response => {
        if(response.ok) {
          return response.json()
        } else {
          return { success: false, message: response.statusText }
        }})
      .then( data => {
        if(data.success) {
          view.passwordInput.value = '';
          // force reloading the user information by forcing call to initPage
          document.dispatchEvent(new Event("DOMContentLoaded"));
        } else {
          M.toast({html: "Fehler beim Anmelden: " + data.message, classes: "tfat-error rounded"});
        }
      });
  },

  changePassword() {
    let request = new Request('./index.php/user/changePassword',{
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({
        password : view.passwordInput.value,
        newpassword: view.newPasswordInput.value,
        newpasswordrepeat: view.passwordRepeatInput.value}
    )}); 
    fetch(request)
      .then( response => {
        if(response.ok) {
          return response.json()
        } else {
          return { success: false, message: response.statusText }
        }})
      .then( data => {
        if(data.success) {
          M.toast({html: "Passwort erfolgreich geändert.", classes: "tfat-success rounded"});
          view.passwordInput.value = '';
          view.newPasswordInput.value = '';
          view.passwordRepeatInput.value = '';
        } else {
          M.toast({html: "Fehler beim Ändern des Passworts: " + data.message, classes: "tfat-error rounded"});
        }
    }); 
  }
}
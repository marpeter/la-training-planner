import { initPage } from "./common.js";
import { User } from "./user-model.js";

initPage(continueInitPage);

function continueInitPage(user) {
  User.loadAll().then( () => {
      view.finishUi({
        loggedInUser: user,
        users: User.getAll(),
        selectedUser: undefined,
        userListFilter: "",
      });
      controller.registerEventHandlers();    
    }
  );
}

const NULL_USER = new User('');

const view = {
  model: undefined,
  // properties to remember elements of the user form
  userName: undefined,
  userPassword: undefined,
  userRole: undefined,

  finishUi(model) {
    this.model = model;
      
    // this.userId = document.getElementById("user-id");
    this.userName = document.getElementById("user-name");
    this.userRole = document.getElementById("user-role");
    this.userPassword = document.getElementById("user-password");

    this.fillUserList();

    // TODO: remove role-option "superuser" if the current user is no superuser
    this.updateUserForm();
  },

  // fill the user list with the users from the model
  fillUserList() {
    let userList = document.getElementById("user-list");
    // clear the list, keeping the header (first child)
    while(userList.childElementCount > 1) { userList.removeChild(userList.lastChild);}

    User.getAll().forEach( (user) => {
      if (user.name.startsWith(this.model.userListFilter)) {
        let item = document.createElement("li");
        item.classList.add("collection-item", "left-align", "tfat-background");
        let anchor = document.createElement("a");
        anchor.classList.add("tfat-text", "darken-4");
        anchor.innerHTML = user.name;
        anchor.setAttribute("href", "#!");
        anchor.setAttribute("data-id", user.id);
        anchor.onclick = () => controller.onUserSelected(user.id);
        item.appendChild(anchor);
        userList.appendChild(item);
      }
    });
  },

  // update the exerise form with the selected user
  updateUserForm() {
    let editing, creating;
    let user = this.model.selectedUser;
    if (user === undefined) {
      editing = false;
      creating = this.model.loggedInUser.canEdit;
      user = NULL_USER;
    } else {
      // enable the form only if the app version supports editing and the user is permitted to
      creating = false;
      editing = this.model.loggedInUser.canEdit;
    }

    // this.userId.value = user.id;
    this.userName.value = user.name;
    this.userPassword.value = '';
    Array.from(this.userRole.options)
         .forEach( (option) => option.selected = (user.role === option.value) );
    
    this.userName.disabled = !creating;
    this.userRole.disabled =
    this.userPassword.disabled = !editing && !creating;

    if (editing) {
      document.getElementById("save-user").classList.remove("disabled");
      document.getElementById("delete-user").classList.remove("disabled");
    } else {
      document.getElementById("save-user").classList.add("disabled");
      document.getElementById("delete-user").classList.add("disabled");
    }
    if(creating) {
      document.getElementById("create-user").classList.remove("disabled");
    } else {
      document.getElementById("create-user").classList.add("disabled")
    }

    M.updateTextFields();
    M.FormSelect.init(document.getElementById("user-role"));
  },
}

const controller = {
    registerEventHandlers() {
      document.getElementById("user-filter").oninput = this.onUserFilterChanged;
      document.getElementById("create-user").onclick = this.onCreateUser;
      document.getElementById("save-user").onclick = this.onSaveChanges;
      document.getElementById("delete-user").onclick = this.onDeleteUser;
      // initialize the static select elements
      M.FormSelect.init(document.querySelectorAll('select'));
      // initialize the modal for the delete confirmation
      M.Modal.init(document.querySelectorAll('.modal'));
      document.getElementById("confirm-delete-yes").onclick = this.onDeleteUserConfirmed;
      document.getElementById("confirm-delete-no").onclick = this.onDeleteUserCancelled;
    },

    onUserFilterChanged(event) {
      view.model.userListFilter = event.target.value;
      view.fillUserList();
    },

    onUserSelected(userId) {
      view.model.selectedUser = User.getAll().find( (user) => user.id==userId );
      view.updateUserForm();
    },


    getUserFromForm() {
      let selectedRole = view.userRole.selectedIndex;
      if( selectedRole < 0 ) { // no role selected
        selectedRole = '';
      } else {
        selectedRole = view.userRole.options.item(selectedRole).value;
      }
      let userOnForm;
      if( view.model.selectedUser == undefined ) {
        userOnForm = new User(
        view.userName.value,
        view.userPassword.value,
        selectedRole)
      } else {
        userOnForm = view.model.selectedUser;
        userOnForm.name = view.userName.value;
        userOnForm.password = view.userPassword.value;
        userOnForm.role = selectedRole;

      }
      return userOnForm;
    },

    async onSaveChanges(event) {
      // save the changes
      controller.saveUserChanges().then( () => view.updateUserForm() );
    },


    saveUserChanges() {
      let okay = document.forms["user-edit"].checkValidity();
      if(!okay) {
        M.toast({html: "Bitte fülle alle Felder korrekt aus.", classes: "tfat-error rounded"});
      } else {
        let modifiedUser = controller.getUserFromForm();
        return modifiedUser.save()
        .then(data => {
          if(data.success) {
            view.model.selectedUser = User.getAll().find(user => user.id === data.data.id);
            M.toast({html: "Benutzer erfolgreich gespeichert.", classes: "tfat-success rounded"});
          } else {
            console.error("Error saving user:", data);
            M.toast({html: "Fehler beim Speichern des Benutzers: " + data.message, classes: "tfat-error rounded"});
          }
        })
        .then( () =>  view.fillUserList() ) // update the user list in case a user was created or a name changed
        .catch(error => {
          console.error("Error saving user:", error);
          M.toast({html: "Fehler beim Speichern des Benutzers.", classes: "tfat-error rounded"});
        });
      }
    },

    async onCreateUser() {
      controller.saveUserChanges().then( () => {
        view.updateUserForm(); // update the form to reflect the changes
      });
    },
      
    onDeleteUser() {
      let confirmDialog = M.Modal.getInstance(document.getElementById("confirm-delete"));
      confirmDialog.options.dismissible = false;
      document.getElementById("confirm-delete-content").innerHTML = 
        `<h4>Benutzer löschen.</h4><p>Soll Benutzer <strong>${view.model.selectedUser.name}</strong> wirklich gelöscht werden?</p>`;
      confirmDialog.open();
    },

    onDeleteUserConfirmed() {
      M.Modal.getInstance(document.getElementById("confirm-delete"))
             .close();
      view.model.selectedUser.delete()
      .then(data => {
        if(data.success) {
          M.toast({html: "Benutzer erfolgreich gelöscht.", classes: "green accent-3 rounded"});
          view.model.selectedUser = undefined; // clear the selected user
          view.fillUserList();
          view.updateUserForm();
        } else {
          M.toast({html: "Fehler beim Löschen des Benutzers: " + data.message, classes: "tfat-error rounded"});
        }
      }).catch(error => {
        console.error("Error saving user:", error);
        M.toast({html: "Fehler beim Löschen des Benutzes.", classes: "tfat-error rounded"});
      }); 
    },

    onDeleteUserCancelled() {
      M.Modal.getInstance(document.getElementById("confirm-delete"))
             .close();
    },
}
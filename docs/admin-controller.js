import { dbVersion } from "./data/db.js";

document.addEventListener('DOMContentLoaded', function() {
    dbVersion().then( (result) => {
        let uiModel = {
          version: result,
        };
        view.finishUi(uiModel);
        controller.registerEventHandlers();    
    });
  });

  const view = {
    model: undefined,
    finishUi(model) {
        this.model = model,
        this.updateVersion();
      },

  // update the version number in the footer of the page and enable the edit button if the version supports editing
  updateVersion() {
    let versionElement = document.getElementById("version");
    console.log("Version: " + JSON.stringify(this.model.version));
    versionElement.innerHTML = this.model.version.number;
    if(this.model.version.supportsDownload) {
      [ "downloadDisciplinesBtn", "downloadExercisesBtn", "downloadFavoritesBtn", "uploadDataBtn" ].forEach( (element) => {
        const elementToEnable = document.getElementById(element);
        elementToEnable.classList.remove("disabled");
       });
       [ "DisciplinesFile", "ExercisesFile", "FavoritesFile"].forEach( (element) => {
        const elementToEnable = document.getElementById(element);
        elementToEnable.disabled=false;
      });
    };
    if(this.model.version.supportsEditing) {
      let editBtn = document.getElementById("editBtn");
      editBtn.classList.remove("disabled");
    }
  },

}

const controller = {
    registerEventHandlers() {
    },

}
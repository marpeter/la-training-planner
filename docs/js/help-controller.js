import { App } from "./model.js";
import { updateCommonUiElements } from "./common.js";

document.addEventListener('DOMContentLoaded', () => {
  App.getVersion().then( (version) => {
    updateCommonUiElements(version);
  });
});
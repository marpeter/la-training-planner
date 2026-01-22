class Backend {
  static #version = undefined;
  static #user = {};
  static #binding = {};

  static async bind(pathPrefix="./") {
    if( !this.#version) {
      try {
        let version = await fetch(pathPrefix + "data/db_version.php")
          .then( (response) => {
            if(!response.ok) {
              throw new Error(`HTTP error accessing db_version.php: ${response.status}`);
            }
            return response.json();
        });

        this.#version = {
          date: version.date,
          number: version.number,
          withBackend: true,
          withDB: convertIfBoolean(version.withDB),
        };

        this.#user = {
          name: version.username,
          role: version.userrole,
          canEdit: convertIfBoolean(version.supportsEditing),
        };

        if(this.#version.withDB) {
          this.#version.numberText = version.number + " (mit Backend Datenbank)";
          this.#binding.disciplineLoader = loadFromDbTable.bind(null,pathPrefix,"discipline");
          this.#binding.exerciseLoader   = loadFromDbTable.bind(null,pathPrefix,"exercise");
          this.#binding.favoritesLoader  = loadFromDbTable.bind(null,pathPrefix,"favorite");
          this.#binding.exerciseSaver = saveToDb.bind(null,pathPrefix,"exercise");
          this.#binding.favoritesSaver = saveToDb.bind(null,pathPrefix,"favorite");
        } else {
          this.#version.numberText = version.number + " (mit PHP Backend, aber noch ohne Datenbank)";
          this.#binding.disciplineLoader = loadDisciplinesFromCSV.bind(null,pathPrefix);
          this.#binding.exerciseLoader = loadExercisesFromCsv.bind(null,pathPrefix);
          this.#binding.favoritesLoader = loadFavoritesFromCsv.bind(null,pathPrefix);
        }
      } catch (error) {
        console.error("Error loading version from DB: " + error + " --> assuming there is no PHP/DB backend.");
        this.#version = {
          number: "0.13.6",
          numberText: "0.13.6 (ohne Backend Datenbank)",
          withBackend: false,
        };
        this.#user = {
          name: "guest",
          role: "guest",
          canEdit: false,
        };
        this.#binding = {
          disciplineLoader: loadDisciplinesFromCSV.bind(null,pathPrefix),
          exerciseLoader:   loadExercisesFromCsv.bind(null,pathPrefix),
          favoritesLoader:  loadFavoritesFromCsv.bind(null,pathPrefix),
        };
      }
      console.log("App version info: " + JSON.stringify(this.#version));
    }
  }

  static getVersionAndUserInfo() {
    return {version: this.#version, user: this.#user};
  }

  static getBinding() {
    return this.#binding;
  }

};

// load (read) all instances of an entity from the database and return it as JSON
async function loadFromDbTable(pathPrefix,entityName) {
  return fetch(`${pathPrefix}index.php/entity/${entityName}`).then( (response) => {
    if(!response.ok) throw new Error(`HTTP error: ${response.status}`);
    return response.json();
  }).catch( (error) => {
    console.error(`Error loading table ${entityName}: ` + error);
  });
}

// save an entity to the database:
//   entityName: exercise or favorite
//   verb: create, update, or delete
async function saveToDb(pathPrefix,entityName,action,data) {
  let request = new Request(`${pathPrefix}index.php/entity/${entityName}`, {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams({ verb: action.toLowerCase(), data : JSON.stringify(data)})
  });
  return fetch(request).then(response => {
    if(!response.ok) throw new Error(`HTTP error saving ${entityName}: ${response.status}`);
    return response.json();
  });
};

function convertCsv(csv, fieldSeparator=/,/) {
  const newLine = /\r?\n/;
  let lines = csv.split(newLine);
  let header = lines.shift().split(fieldSeparator) // the first line contains the column headers
    .map( (field) => field.toLowerCase() )         // convert field names to lowercase
    .map( (field) => field.endsWith("[]") ?        // detect fields the values of which are lists
      { name: field.replace("[]",""), makeArray: true } : { name: field, makeArray: false }  ); 
  let result = [];
  lines.forEach( (line) => {
    if(line.trim() != "") { // ignore empty lines
      let fields = line.split(fieldSeparator);
      let entry = {};
      header.forEach( (field, index) => {
        let value = fields[index].replaceAll('"',''); // replaceAll to get rid if text-delimiting " characters added by MS Excel and php
        if(field.makeArray) {
          entry[field.name] = value=="" ? [] : value.split(':').map( (val) => convertIfBoolean(val) );
        } else {
          entry[field.name] = convertIfBoolean(value);
        }
       });
      result.push(entry);
    }
  });
  return result;
};

async function loadExercisesFromCsv(pathPrefix) {
  let csv = await loadCsvFile(pathPrefix + 'data/Exercises.csv');
  let exercises = convertCsv(csv, /;/);
  exercises.forEach( (exercise) => {
    exercise.durationmin = parseInt(exercise.durationmin);
    exercise.durationmax = parseInt(exercise.durationmax);
  } );
  // Add the "Auslaufen" exercise. It's not in the CSV to prevent it from being modified by mistake
  exercises.push({ id: "Auslaufen", name: "Auslaufen", warmup: false, runabc: false, mainex: false, ending: false, material: "", durationmin: 5, durationmax: 5, repeats: "2 Runden", disciplines: [], details: []});
  return exercises;  
};

async function loadDisciplinesFromCSV(pathPrefix) {
  let csv = await loadCsvFile(pathPrefix + 'data/Disciplines.csv');
  let disciplines = convertCsv(csv);
  return disciplines;  
}

async function loadFavoritesFromCsv(pathPrefix) {
  let csv = await loadCsvFile(pathPrefix + 'data/Favorites.csv');
  let parts = csv.split(/\r?\n\r?\n/); // split on double newlines = empty lines
  let headers = convertCsv(parts[0]);
  let exerciseMap = convertCsv(parts[1]);
  return {headers, exerciseMap};  
}

async function loadCsvFile(fileName) {
  try {
    let csv = await fetch(fileName).then( (response) => {
      if(!response.ok) throw new Error(`HTTP error accessing ${fileName}: ${response.status}`);
      return response.text();
    });
    return csv;  
  } catch(error) {
    alert(error);
  };  
}

function convertIfBoolean(value) {
  if(value=="true") {
    return true;
  } else if(value=="false") {
    return false;
  } else {
   return value;
  } 
}

export { Backend };
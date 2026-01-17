let version = {};

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

function convertIfBoolean(value) {
  if(value=="true") {
    return true;
  } else if(value=="false") {
    return false;
  } else {
   return value;
  } 
}

async function dbVersion(pathPrefix="") {
  try {
    version = await fetch(pathPrefix + "data/db_version.php").then( (response) => {
      if(!response.ok) throw new Error(`HTTP error accessing db_version.php: ${response.status}`);
      return response.json();
    });
    version.withBackend = true;
    version.withDB = convertIfBoolean(version.withDB);
    version.supportsEditing = convertIfBoolean(version.supportsEditing);
    if(version.withDB) {
      version.number = version.number + " (mit Backend Datenbank)";
      version.disciplineLoader = loadFromDbTable.bind(null,pathPrefix,"disciplines");
      version.exerciseLoader   = loadFromDbTable.bind(null,pathPrefix,"exercises");
      version.favoritesLoader  = loadFromDbTable.bind(null,pathPrefix,"favorites");
      version.exerciseSaver = saveToDb.bind(null,pathPrefix,"exercise");
      version.favoritesSaver = saveToDb.bind(null,pathPrefix,"favorite");
    } else {
      version.number = version.number + " (mit PHP Backend, aber noch ohne Datenbank)";
      version.disciplineLoader = loadDisciplinesFromCSV.bind(null,pathPrefix);
      version.exerciseLoader = loadExercisesFromCsv.bind(null,pathPrefix);
      version.favoritesLoader = loadFavoritesFromCsv.bind(null,pathPrefix);
    }
  } catch (error) {
    console.error("Error loading version from DB: " + error + " --> assuming there is no PHP/DB backend.");
    version = {
      number: "0.13.5 (ohne Backend Datenbank)",
      withBackend: false,
      supportsEditing:   false,
      disciplineLoader:  loadDisciplinesFromCSV.bind(null,pathPrefix),
      exerciseLoader:    loadExercisesFromCsv.bind(null,pathPrefix),
      favoritesLoader:   loadFavoritesFromCsv.bind(null,pathPrefix),
    };
  }
  return version;
}

async function loadFromDbTable(pathPrefix,table) {
  return fetch(`${pathPrefix}data/db_read.php?entity=${table}`).then( (response) => {
    if(!response.ok) throw new Error(`HTTP error: ${response.status}`);
    return response.json();
  }).catch( (error) => {
    console.error(`Error loading table ${table}: ` + error);
  });
}

// save an entity to the database:
//   entityName: exercise or favorite
//   verb: create, update, or delete
async function saveToDb(pathPrefix,entityName,action,data) {
  let request = new Request(pathPrefix + "edit/db_update.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams({ verb: action.toLowerCase(),  entity: entityName.toLowerCase(), data : JSON.stringify(data)})
  });
  return fetch(request).then(response => {
    if(!response.ok) throw new Error(`HTTP error saving exercise: ${response.status}`);
    return response.json();
  });
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

export { dbVersion };
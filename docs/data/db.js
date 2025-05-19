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

async function dbVersion() {
  try {
    version = await fetch("data/db_version.php").then( (response) => {
      if(!response.ok) throw new Error(`HTTP error accessing db_version.php: ${response.status}`);
      return response.json();
    });
    version.withDB = convertIfBoolean(version.withDB);
    if(version.withDB) {
      version.number = version.number + " (mit Backend Datenbank)";
      version.supportsFavorites = convertIfBoolean(version.supportsFavorites);
      version.supportsEditing   = convertIfBoolean(version.supportsEditing);
      version.supportsDownload   = true;
      version.disciplineLoader = loadFromDbTable.bind(null,"disciplines");
      version.exerciseLoader   = loadFromDbTable.bind(null,"exercises");
      version.favoritesLoader  = loadFromDbTable.bind(null,"favorites");
    } else {
      version.number = version.number + " (mit PHP Backend, aber noch ohne Datenbank)";
      version.supportsFavorites = false;
      version.supportsEditing   = false;
      version.supportsDownload   = false;
      version.disciplineLoader = loadDisciplinesFromCSV;
      version.exerciseLoader = loadExercisesFromCsv;
      version.favoritesLoader = loadFavoritesFromCsv;
    }
  } catch (error) {
    console.error("Error loading version from DB: " + error + " --> assuming there is no PHP/DB backend.");
    version = {
      number: "0.13.3 (ohne Backend Datenbank)",
      supportsFavorites: false,
      supportsEditing:   false,
      supportsDownload:  false,
      disciplineLoader:  loadDisciplinesFromCSV,
      exerciseLoader:    loadExercisesFromCsv,
      favoritesLoader:   loadFavoritesFromCsv,
    };
  }
  return version;
}

async function loadFromDbTable(table) {
  return fetch(`data/db_read_${table}.php`).then( (response) => {
    if(!response.ok) throw new Error(`HTTP error: ${response.status}`);
    return response.json();
  }).catch( (error) => {
    console.error(`Error loading table ${table}: ` + error);
  });
}

async function loadExercisesFromCsv() {
  try {
    let csv = await fetch('data/Exercises.csv').then( (response) => {
      if(!response.ok) throw new Error(`HTTP error accessing db_version.php: ${response.status}`);
      return response.text();
    });
    let exercises = convertCsv(csv, /;/);
    exercises.forEach( (exercise) => {
      exercise.durationmin = parseInt(exercise.durationmin);
      exercise.durationmax = parseInt(exercise.durationmax);
    } );
    // Add the "Auslaufen" exercise. It's not in the CSV to prevent it from being modified by mistake
    exercises.push({ id: "Auslaufen", name: "Auslaufen", warmup: false, runabc: false, mainex: false, ending: false, material: "", durationmin: 5, durationmax: 5, repeats: "2 Runden", disciplines: [], details: []});
    return exercises;  
  } catch(error) {
    alert(error);
  };
}

async function loadDisciplinesFromCSV() {
  try {
    let csv = await fetch('data/Disciplines.csv').then( (response) => {
      if(!response.ok) throw new Error(`HTTP error accessing db_version.php: ${response.status}`);
      return response.text();
    });
    let disciplines = convertCsv(csv);
    return disciplines;  
  } catch(error) {
    alert(error);
  };
}

async function loadFavoritesFromCsv() {
  try {
    let csv = await fetch('data/Favorites.csv').then( (response) => {
      if(!response.ok) throw new Error(`HTTP error accessing db_version.php: ${response.status}`);
      return response.text();
    });
    let parts = csv.split(/\r?\n\r?\n/); // split on double newlines = empty lines
    let headers = convertCsv(parts[0]);
    let exerciseMap = convertCsv(parts[1]);
    return {headers, exerciseMap};  
  } catch(error) {
    alert(error);
  };
}

export { dbVersion };
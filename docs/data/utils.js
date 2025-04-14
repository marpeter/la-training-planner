async function loadObjectFromCSV(url, fieldSeparator=/,/) {
  const newLine = /\r?\n/;
  return fetch(url).then( (response) => {
    if(!response.ok) throw new Error(`HTTP error: ${response.status}`);
    return response.text();
  }).then( (csv) => {
    let lines = csv.split(newLine);
    let header = lines.shift().split(fieldSeparator) // the first line contains the column headers
      .map( (field) => field.toLowerCase() )         // convert field names to lowercase
      .map( (field) => field.endsWith("[]") ?        // detect fields the values of which are lists
        { name: field.replace("[]",""), makeArray: true } : { name: field, makeArray: false }  ); 
    let result = {};
    lines.forEach( (line) => {
      let fields = line.split(fieldSeparator);
      let id = fields[0]; // the first field must be the property name in the result object
      let entry = {};
      header.forEach( (field, index) => {
        let value = fields[index].replaceAll('"',''); // replaceAll to get rid if text-delimiting " characters added by MS Excel
        if(field.makeArray) {
          entry[field.name] = value=="" ? [] : value.split(':').map( (val) => convertIfBoolean(val) );
        } else {
          entry[field.name] = convertIfBoolean(value);
        }
       });
      result[id] = entry;
    });
    return result;
  }).catch( (error) => {
    alert(error);
  });
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

export { loadObjectFromCSV };
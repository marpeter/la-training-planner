async function loadObjectFromCSV(url, fieldSeparator=/,/) {
  const newLine = /\r?\n/;
  return fetch(url).then( (response) => {
    if(!response.ok) throw new Error(`HTTP error: ${response.status}`);
    return response.text();
  }).then( (csv) => {
    let lines = csv.split(newLine);
    const header = lines.shift().split(fieldSeparator).map( (field) => field.toLowerCase() ); // the first line contains the column headers
    let result = {};
    lines.forEach( (line) => {
      let fields = line.split(fieldSeparator);
      let id = fields[0]; // the first field must be the property name in the result object
      let entry = {};
      header.forEach( (field, index) => {
        let value = fields[index].replaceAll('"',''); // replaceAll to get rid if text-delimiting " characters added by MS Excel
        if(value=="true") {
          entry[field] = true;
        } else if(value=="false") {
          entry[field] = false;
        } else {
          entry[field] = value;
        }
       });
      result[id] = entry;
    });
    return result;
  }).catch( (error) => {
    alert(error);
  });
};

export { loadObjectFromCSV };
async function loadObjectFromCSV(url) {
  const newLine = /\r?\n/;
  const separator = /,/;
  return fetch(url).then( (response) => {
    if(!response.ok) throw new Error(`HTTP error: ${response.status}`);
    return response.text();
  }).then( (csv) => {
    let lines = csv.split(newLine);
    const header = lines.shift().split(separator); // the first line contains the column headers
    let result = {};
    lines.forEach( (line) => {
      let fields = line.split(separator);
      let id = fields.shift(); // the first field must be the property name in the result object
      let entry = {};
      fields.forEach( (value, index) => { 
        entry[header[index+1]] = value;
       });
      result[id] = entry;
    });
    return result;
  }).catch( (error) => {
    alert(error);
  });
};

// the following does not work, at least not locally using plain HTTP:
// const Disciplines = loadObjectFromCSV('data/Disciplines.csv');
// export default await Disciplines;

export { loadObjectFromCSV };
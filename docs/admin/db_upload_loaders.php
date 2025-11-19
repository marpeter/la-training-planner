<?php
namespace LaPlanner;

class ParseException extends \Exception {}

class CsvParser {
    private $separator = ',';
    private $dataLength = 0;
 
    public function __construct($separator=',') {
        $this->separator = $separator;
    }
    /**
     * Takes an array of strings
     * @param $tableLines assumed to be lines of a few tables, with different
     *        tables separated empty lines
     * @param $expectedFields an array of arrays, each internal array the
     *        field names in the table and declared in its first line (header)
     * @param $tableNames and the name of the tables (for error messages)
     * @return an array of assiciative arrays representing the tables if
     *         parsing was successful,
     * @throws ParseException otherwise
     */
    public function parseTables($tableLines, $expectedFields, $tableNames) {
        $this->dataLength = count($tableLines);
        $tableData = [];
        $tableStartLine = 0;
        $tableNo = 0;
        while( $tableStartLine < $this->dataLength ) {
            $fieldMap = $this->getFieldMapping($tableLines[$tableStartLine],
                $expectedFields[$tableNo], $tableNames[$tableNo]);
            $tableData[$tableNo] = $this->parseSingleTable($tableLines,
                $expectedFields[$tableNo], $fieldMap, ++$tableStartLine);
            $tableStartLine += count($tableData[$tableNo]) + 1; // for separator line
            $tableNo++;
        }
        return $tableData;
    }
    /** 
     * Takes an array of strings
     * @param $headerLine considered the header line of a table,
     * @param $expectedFields an array of field names expected in the header
     * @param $tableName and the name of the table (for error messages)
     * @return an array of field positions in the table if the line is valid,
     * @throws ParseException otherwise
     */
    public function getFieldMapping($headerLine, $expectedFields, $tableName) {
        // preg_replace removes a potential
        // \xEFBBBF UTF8 "ZERO WIDTH NO-BREAK SPACE" character at the beginning
        $header = explode($this->separator, preg_replace("/\xEF\xBB\xBF/", "", $headerLine));
        $expectedFieldCount = count($expectedFields);
        if( count($header) !== $expectedFieldCount ) {
            throw new ParseException("Die Kopfzeile der $tableName-Daten $headerLine enthält nicht die erwartete Anzahl an $expectedFieldCount Spalten.");
        }
        // Convert field names to lower case
        foreach($header as &$field) {
            $field = strtolower(trim($field));
        }
        // Check that only valid field names are used
        if(count(array_diff($header,$expectedFields))>0) {
            $expectedStr = implode(',',$expectedFields);
            $actualStr = implode(',', array_diff($header,$expectedFields));
            throw new ParseException("Die Kopfzeile der $tableName Daten $headerLine enthält nicht die erwarteten Spaltennamen $expectedStr, der Unterschied ist $actualStr.");
        }
        // "pivot" the header
        $fieldMap = [];
        foreach($expectedFields as $field) {
            $fieldMap[$field] = array_search($field, $header);
        }
        return $fieldMap;
    }
    /** 
     * Converts the array of strings
     * @param $tableLines assumed to be lines of a few tables, with different
     *        tables separated empty lines, starting from line
     * @param $from assumed to be the first line of table number
     * @param $entityNo, into an array of associative arrays, with
     * @param $fieldNames the names of the fields in the table / associative array,
     * @param $fieldMap mapping field names to positions in the lines
     * @return table as associative array.
     */
    protected function parseSingleTable($tableLines, $fieldNames, $fieldMap, $from=1) {
        $tableLoaded = [];
        for($lineNo=$from; $lineNo<$this->dataLength; $lineNo++) {
            if( strlen(trim($tableLines[$lineNo])) == 0) { // empty line separating different tables reached
                break;
            }
            $tableLoaded[] = $this->parseLine($tableLines[$lineNo], $fieldNames, $fieldMap, $this->separator);
        }
        return $tableLoaded;
    }
    private function parseLine($line, $fieldNames, $fieldMap) {
        $values = explode($this->separator, $line);
        $fields = [];
        foreach($fieldNames as $field) {
            if( str_ends_with($field,'[]') ) { // "array" field
                $fields[$field] = explode(':', trim($values[$fieldMap[$field]]));
            } else {
                $fields[$field] = trim($values[$fieldMap[$field]]," \"\n\r\t\v\x00");
            }
        }
        return $fields;
    }
}

abstract class DataLoader {
    // override these properties in each concrete class
    protected $headerFields = [];
    protected $entityNames = [];
    protected $entityTableNames = [];
    protected $messages = [];
    protected $dbConnection = null;
    protected $csvParser = null;

    public function __construct() {
        $this->csvParser = new CsvParser();
    }

    public function load($data, &$messages) {
        $this->messages = &$messages;
        try {
            $tableContent = $this->csvParser->parseTables($data, $this->headerFields, $this->entityTableNames);
        } catch(ParseException $ex) {
            $this->messages[] = $ex->getMessage();
            return 0;
        }
        // process the data
        $this->dbConnection = connectDB();
        $this->dbConnection->beginTransaction();
        // 1. remove current database entries
        $okay = $this->clearCurrentEntries();
        // 2. insert the uploaded disciplines
        $okay = $this->insertEntries($tableContent);
        // 3. commit or rollback
        if($okay) {
            $this->dbConnection->commit();
            $c1 = count($tableContent[0]);
            $messages[] = "Es wurden $c1 {$this->entityNames[0]}en geladen.";
            return 1;
        } else {
            $this->dbConnection->rollBack();
            $messages[] = "Es wurden keine {$this->entityNames[0]}en geladen.";
            return 0;
        }
    }
    protected function clearCurrentEntries() {
        try {
            foreach($this->entityTableNames as $tableName) {
                $result = $this->dbConnection->exec("DELETE FROM $tableName");
            }
            return true;
        } catch( \PDOException $ex) {
            $error = $ex->getMessage();
            $this->messages[] = "Löschen der vorhanden {$this->entityNames[0]} Daten fehlgeschlagen, Fehler: $error.";
            return false;
        }      
    }
    abstract protected function insertEntries($tableEntries);
}

class DisciplineLoader extends DataLoader {
    protected $headerFields = [['id', 'name', 'image']];
    protected $entityNames = ['Disziplin'];
    protected $entityTableNames = ['DISCIPLINES'];

    protected function insertEntries($tableEntries) {
        $stmt = $this->dbConnection->prepare('INSERT INTO DISCIPLINES ' . 
            '( id,  name,  image) VALUES ' . 
            '(:id, :name, :image)');
        $stmt->bindParam('id', $id, \PDO::PARAM_STR);
        $stmt->bindParam('name', $name, \PDO::PARAM_STR);
        $stmt->bindParam('image', $image, \PDO::PARAM_STR);
        $okay = true;
        foreach($tableEntries[0] as ['id' => $id, 'name' => $name, 'image' => $image]) {
            try {
                $stmt->execute();
            } catch( \PDOException $ex) {
                $error = $ex->getMessage();
                $this->messages[] =  "Einfügen der Disziplin-Zeile $id, $name, $image ist fehlgeschlagen, Fehler: $error.";
                $okay = false;
            }
        }
        return $okay;
    }
}

class ExerciseLoader extends DataLoader {
    protected $headerFields = [ ['id', 'name', 'warmup', 'runabc', 'mainex', 'ending', 
        'material', 'durationmin', 'durationmax', 'repeats', 'disciplines[]', 'details[]']];
    protected $entityNames = ['Übung'];
    protected $entityTableNames = ['EXERCISES WHERE NOT id="Auslaufen"', 'EXERCISES_DISCIPLINES'];

    public function __construct() {
        $this->csvParser = new CsvParser(';');
    }

    protected function insertEntries($tableEntries) {
        $okay = true;
        $stmt_header = $this->dbConnection->prepare('INSERT INTO EXERCISES ' .
            '( id,  name,  warmup,  runabc,  mainex,  ending,  material,  durationmin,  durationmax,  repeats,  details) VALUES ' . 
            '(:id, :name, :warmup, :runabc, :mainex, :ending, :material, :durationmin, :durationmax, :repeats, :details)');
        $stmt_header->bindParam('id', $id, \PDO::PARAM_STR);
        $stmt_header->bindParam('name', $name, \PDO::PARAM_STR);
        $stmt_header->bindParam('warmup', $warmup, \PDO::PARAM_INT);
        $stmt_header->bindParam('runabc', $runabc, \PDO::PARAM_INT);
        $stmt_header->bindParam('mainex', $mainex, \PDO::PARAM_INT);
        $stmt_header->bindParam('ending', $ending, \PDO::PARAM_INT);
        $stmt_header->bindParam('durationmin', $durationmin, \PDO::PARAM_INT);
        $stmt_header->bindParam('durationmax', $durationmax, \PDO::PARAM_INT);
        $stmt_header->bindParam('material', $material, \PDO::PARAM_STR);
        $stmt_header->bindParam('repeats', $repeats, \PDO::PARAM_STR);
        $stmt_header->bindParam('details', $details, \PDO::PARAM_STR);           
        $stmt_dscplns = $this->dbConnection->prepare('INSERT INTO EXERCISES_DISCIPLINES (exercise_id, discipline_id) VALUES (:id,:discipline_id)');
        $stmt_dscplns->bindParam('id', $id, \PDO::PARAM_STR);
        $stmt_dscplns->bindParam('discipline_id', $discipline_id, \PDO::PARAM_STR);
        foreach($tableEntries[0] as ['id' => $id, 'name' => $name, 'warmup' => $warmup,
              'runabc' => $runabc, 'mainex' => $mainex, 'ending' => $ending, 'material' => $material,
              'durationmin' => $durationmin, 'durationmax' => $durationmax, 'repeats' => $repeats,
              'details[]' => $detailsArray, 'disciplines[]' => $disciplines]) {
            $details = implode(':', $detailsArray);
            // convert boolean values
            $warmup = $warmup=="true" ? 1 : 0;
            $runabc = $runabc=="true" ? 1 : 0;
            $mainex = $mainex=="true" ? 1 : 0;
            $ending = $ending=="true" ? 1 : 0;
            try {
                $stmt_header->execute();
                foreach($disciplines as $discipline_id) {
                    try {
                        $stmt_dscplns->execute();
                    } catch(\PDOException $ex) {
                        $error = $ex->getMessage();
                        $messages[] = 'Hinzufügen der Disziplin $discipline_id zu Übung $id ist fehlgeschlagen, Fehler: $error.';
                        $okay = false;
                    }                    
                }
            } catch(\PDOException $ex) {
                $error = $ex->getMessage();
                $messages[] =  'Einfügen der Übungs-Zeile $id, $name, ... ist fehlgeschlagen, Fehler: $error.';
                $okay = false;
            }
        }
        return $okay;
      }
}

class FavoriteLoader extends DataLoader {
    protected $headerFields = [['id', 'created_by', 'created_at', 'description', 'disciplines[]'],
                               ['favorite_id', 'phase', 'position', 'exercise_id', 'duration']];
    protected $entityNames = ['Favoriten', 1 => 'FavoritenÜbungen'];
    protected $entityTableNames = ['FAVORITE_HEADERS' , 'FAVORITE_DISCIPLINES', 'FAVORITE_EXERCISES'];
    
    protected function insertEntries($tableEntries) {
        $okay = true;
        $stmt_header = $this->dbConnection->prepare('INSERT INTO FAVORITE_HEADERS ' . 
            '( id,  created_by,  created_at,  description) VALUES ' . 
            '(:id, :created_by, :created_at, :description)');
        $stmt_header->bindParam('id', $id, \PDO::PARAM_INT);
        $stmt_header->bindParam('created_by', $created_by, \PDO::PARAM_STR);
        $stmt_header->bindParam('created_at', $created_at, \PDO::PARAM_STR);
        $stmt_header->bindParam('description', $descr, \PDO::PARAM_STR);
        $stmt_dscplns = $this->dbConnection->prepare('INSERT INTO FAVORITE_DISCIPLINES (favorite_id, discipline_id) VALUES (:id,:discipline_id)');
        $stmt_dscplns->bindParam('id', $id, \PDO::PARAM_INT);
        $stmt_dscplns->bindParam('discipline_id', $discipline_id, \PDO::PARAM_STR);
        foreach($tableEntries[0] as ['id' => $id, 'created_by' => $created_by, 'created_at' => $created_at,
                                      'description' => $descr, 'disciplines[]' => $disciplines]) {
            try {
                $stmt_header->execute();
                foreach($disciplines as $discipline_id) {
                    try {
                        $stmt_dscplns->execute();
                    } catch( \PDOException $ex) {
                        $error = $ex->getMessage();
                        $this->messages[] = "Hinzufügen der Disziplin id=$discipline_id zu Favorit id=$id ist fehlgeschlagen, Fehler: $error.";
                        $okay = false;
                    }
                }
            } catch( \PDOException $ex) {
                $error = $ex->getMessage();
                $this->messages[] = "Einfügen der Favoriten-Zeile id=$id, created_by=$created_by, created_at=$created_at, description=$descr ist fehlgeschlagen, Fehler: $error.";
                $okay = false;
            }
        }
        // 3. insert the exercise-favorite relations
        $stmt_exmap = $this->dbConnection->prepare('INSERT INTO FAVORITE_EXERCISES ' . 
            '( favorite_id,  phase,  position,  exercise_id,  duration) VALUES ' . 
            '(:favorite_id, :phase, :position, :exercise_id, :duration)');
        $stmt_exmap->bindParam(':favorite_id', $favorite_id, \PDO::PARAM_INT);
        $stmt_exmap->bindParam(':phase', $phase, \PDO::PARAM_STR);
        $stmt_exmap->bindParam(':position', $position, \PDO::PARAM_INT);
        $stmt_exmap->bindParam(':exercise_id', $exercise_id, \PDO::PARAM_STR);
        $stmt_exmap->bindParam(':duration', $duration, \PDO::PARAM_INT);
        foreach($tableEntries[1] as ['favorite_id' => $favorite_id, 'phase' => $phase, 'position' => $position,
                                      'exercise_id' => $exercise_id, 'duration' => $duration]) {
            try {
                $stmt_exmap->execute();
            } catch( \PDOException $ex) {
                $error = $ex->getMessage();
                $messages[] = "Übung $exercise_id zu Favorit $favorite_id (Phase $phase, Pos. $position, Dauer $duration) ist fehlgeschlagen, Fehler: $error.";
                $okay = false;
            }
        }
        return $okay;
    }
}
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
     *        tables separated by empty lines
     * @param $expectedFields an array of arrays, each internal array the
     *        field names in the table and declared in its first line (header)
     * @param $tableNames and the name of the tables (for error messages)
     * @return an array of assiciative arrays representing the tables if
     *         parsing was successful,
     * @throws ParseException otherwise
     */
    public function parseTables($tableLines, $expectedFields, $tableNames): array {
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
    public function getFieldMapping($headerLine, $expectedFields, $tableName): array {
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
    protected function parseSingleTable($tableLines, $fieldNames, $fieldMap, $from=1): array {
        $tableLoaded = [];
        for($lineNo=$from; $lineNo<$this->dataLength; $lineNo++) {
            if( strlen(trim($tableLines[$lineNo])) == 0) { // empty line separating different tables reached
                break;
            }
            $tableLoaded[] = $this->parseLine($tableLines[$lineNo], $fieldNames, $fieldMap, $this->separator);
        }
        return $tableLoaded;
    }
    private function parseLine($line, $fieldNames, $fieldMap): array {
        $values = explode($this->separator, $line);
        $fields = [];
        foreach($fieldNames as $field) {
            if( str_ends_with($field,'[]') ) { // "array" field
                $fields[substr($field,0,-2)] = explode(':', trim($values[$fieldMap[$field]]));
            } else {
                $fields[$field] = trim($values[$fieldMap[$field]]," \"\n\r\t\v\x00");
            }
        }
        return $fields;
    }
}

abstract class DataLoader {
    protected $messages = [];
    protected $dbConnection = null;
    // override these properties in each concrete class:
    protected $headerFields = [];
    protected $entityNames = [];
    // initialize these properties in the __construct method of each concrete class:
    protected ?CsvParser $csvParser = null;
    protected ?DataSaver $saver = null;

    public function load($data, &$messages): int {
        $this->messages = &$messages;
        try {
            $tableContent = $this->csvParser->parseTables($data,
                $this->headerFields, $this->getTableNames());
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

    protected function getTableNames(): array {
        return $this->saver->getTableNames();
    }

    protected function clearCurrentEntries(): bool {
        try {
            foreach($this->getTableNames() as $tableName) {
                $result = $this->dbConnection->exec("DELETE FROM $tableName");
            }
            return true;
        } catch( \PDOException $ex) {
            $error = $ex->getMessage();
            $this->messages[] = "Löschen der vorhanden {$this->entityNames[0]} Daten fehlgeschlagen, Fehler: $error.";
            return false;
        }      
    }

    protected function insertEntries($tableEntries): bool {
        $new_messages = $this->saver->createEntityBulk($tableEntries, $this->dbConnection);
        $this->messages = array_merge($this->messages,$new_messages);
        return !$new_messages; // if there are messages, something went wrong
    }
}

class FavoriteLoader extends DataLoader {
    protected $headerFields = [['id', 'created_by', 'created_at', 'description', 'disciplines[]'],
                               ['favorite_id', 'phase', 'position', 'exercise_id', 'duration']];
    protected $entityNames = ['Favorit', 'FavoritenÜbungen'];

    public function __construct() {
        $this->csvParser = new CsvParser();
        $this->saver = new FavoriteSaver();
    }
}
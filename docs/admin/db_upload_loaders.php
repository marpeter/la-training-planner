<?php
namespace LaPlanner;

abstract class DataLoader {
    protected $messages = [];
    protected $headerFields = [];
    protected $entityNames = [];
    protected $entityTableNames = [];
    protected $fieldMaps = [];
    protected $dataLoad = [];
    protected $separator = ',';

    public function load($data, &$messages) {
        $this->messages = &$messages;
        $max = count($data);
        $nextStart = 0;
        $entityNo = 0;
        while($nextStart<$max) {
            $this->fieldMap[$entityNo] = 
                $this->checkHeader($data[$nextStart], $this->headerFields[$entityNo], $this->entityNames[$entityNo], $this->messages, $this->separator);
            if(!$this->fieldMap[$entityNo]) {
                return 0;
            }
            $nextStart = $this->convertUploadedEntries($data, $entityNo, $nextStart+1);
            $entityNo++;
        }
        // process the data
        $dbConnection = connectDB();
        $dbConnection->autocommit(false);
        // 1. remove current database entries
        $okay = $this->clearCurrentEntries($dbConnection);
        // 2. insert the uploaded disciplines
        $okay = $this->insertUploadedEntries($dbConnection);
        // 3. commit or rollback
        if($okay) {
            $dbConnection->commit();
            $dbConnection->close();
            $c1 = count($this->dataLoad[0]);
            $messages[] = "Es wurden $c1 {$this->entityNames[0]}en geladen.";
            return 1;
        } else {
            $dbConnection->rollback();
            $dbConnection->close();
            $messages[] = "Es wurden keine {$this->entityNames[0]}en geladen.";
            return 0;
        }
    }

    protected function clearCurrentEntries($dbConnection) {
        try {
            foreach($this->entityTableNames as $tableName) {
                $result = $dbConnection->query("DELETE FROM $tableName");
            }
            return true;
        } catch( mysqli_sql_exception $ex) {
            $error = $ex->getMessage();
            $this->messages[] = "Löschen der vorhanden {$this->entityNames[0]} Daten fehlgeschlagen, Fehler: $error.";
            return false;
        }      
    }

    protected function convertUploadedEntries($data,$entityNo,$from=1) {
        $this->dataLoad[$entityNo] = [];
        $max = count($data);
        $nextStart = $max;
        for($lineNo=$from; $lineNo<$max; $lineNo++) {
            if(strlen(trim($data[$lineNo]))==0) { // empty line separating different entities reached
                $nextStart = $lineNo+1;
                break;
            }
            $this->dataLoad[$entityNo][] = $this->fields_from_line($data[$lineNo],
            $this->headerFields[$entityNo], $this->fieldMap[$entityNo], $this->separator);
        }
        return $nextStart;
    }

    abstract protected function insertUploadedEntries($dbConnection);

    private function fields_from_line($line, $fieldNames, $fieldMap) {
        $values = explode($this->separator, $line);
        $fields = [];
        foreach($fieldNames as $field) {
            if(str_ends_with($field,'[]')) {
                $fields[$field] = explode(':', trim($values[$fieldMap[$field]]));
            } else {
                $fields[$field] = trim($values[$fieldMap[$field]]," \"\n\r\t\v\x00");
            }
        }
        return $fields;
    }

    private function checkHeader($data, $expected, $dataName) {
        // preg_replace removes a potential \xEFBBBF UTF8 "ZERO WIDTH NO-BREAK SPACE" character at the beginning
        $header = explode($this->separator, preg_replace("/\xEF\xBB\xBF/", "", $data));
        $expectedNum = count($expected);
        if(count($header)!=$expectedNum) {
            $this->messages[] = "Die Kopfzeile der $dataName-Daten $data enthält nicht die erwartete Anzahl an $expectedNum Spalten.";
            return false;
        }
        // Convert field names to lower case
        foreach($header as &$field) { $field = strtolower(trim($field)); }
        // Check that only valid field names are used
        if(count(array_diff($header,$expected))>0) {
            $expectedStr = implode(',',$expected);
            $actualStr = implode(',', array_diff($header,$expected));
            $this->messages[] = "Die Kopfzeile der $dataName Daten $data enthält nicht die erwarteten Spaltennamen $expectedStr, der Unterschied ist $actualStr.";
            return false;
        }
        // "pivot" the header
        $fieldMap = [];
        foreach($expected as $field) {
            $fieldMap[$field] = array_search($field, $header);
        }
        return $fieldMap;
    }

}

class DisciplineLoader extends DataLoader {
    protected $headerFields = [['id', 'name', 'image']];
    protected $entityNames = ['Disziplin'];
    protected $entityTableNames = ['DISCIPLINES'];

    protected function insertUploadedEntries($dbConnection) {
        $stmt_header = $dbConnection->prepare('INSERT INTO DISCIPLINES (id, name, image) VALUES (?, ?, ?)');
        $stmt_header->bind_param('sss', $id, $name, $image);
        $okay = true;
        foreach($this->dataLoad[0] as ['id' => $id, 'name' => $name, 'image' => $image]) {
            try {
                if(!$stmt_header->execute()) {
                    $this->messages[] =  "Einfügen der Disziplin-Zeile $id, $name, $image ist fehlgeschlagen.";
                    $okay = false;
                }
            } catch( mysqli_sql_exception $ex) {
                $error = $ex->getMessage();
                $this->messages[] = "Disziplin $id in die Datenbank laden ist fehlgeschlagen, Fehler: $error.";
                $okay = false;
            }
        }
        $stmt_header->close();
        return $okay;
    }
}

class ExerciseLoader extends DataLoader {
    protected $headerFields = [ ['id', 'name', 'warmup', 'runabc', 'mainex', 'ending', 
      'material', 'durationmin', 'durationmax', 'repeats', 'disciplines[]', 'details[]']];
    protected $entityNames = ['Übung'];
    protected $entityTableNames = ['EXERCISES WHERE NOT id="Auslaufen"', 'EXERCISES_DISCIPLINES'];
    protected $separator = ';';

    protected function insertUploadedEntries($dbConnection) {
        $okay = true;
        $stmt_header = $dbConnection->prepare('INSERT INTO EXERCISES (id, name, warmup, runabc, mainex, ending, material, durationmin, durationmax, repeats, details) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt_header->bind_param('ssiiiisiiss', $id, $name, $warmup, $runabc, $mainex, $ending, $material, $durationmin, $durationmax, $repeats, $details);
        $stmt_dscplns = $dbConnection->prepare('INSERT INTO EXERCISES_DISCIPLINES (exercise_id, discipline_id) VALUES (?,?)');
        $stmt_dscplns->bind_param('ss', $id, $discipline_id);
        foreach($this->dataLoad[0] as ['id' => $id, 'name' => $name, 'warmup' => $warmup,
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
                if(!$stmt_header->execute()) {
                    $messages[] =  'Einfügen der Übungs-Zeile $id, $name, ... ist fehlgeschlagen.';
                    $okay = false;
                } else {
                    foreach($disciplines as $discipline_id) {
                        if(!$stmt_dscplns->execute()) {
                            $messages[] = 'Hinzufügen der Disziplin $discipline_id zu Übung $id ist fehlgeschlagen.';
                            $okay = false;
                        }
                    }
                }
            } catch( mysqli_sql_exception $ex) {
                $error = $ex->getMessage();
                $messages[] = "Übung $id in die Datenbank laden ist fehlgeschlagen, Fehler: $error.";
                $okay = false;
            }
        }
        $stmt_dscplns->close();
        $stmt_header->close();
        return $okay;
      }
}

class FavoriteLoader extends DataLoader {
    protected $headerFields = [['id', 'created_by', 'created_at', 'description', 'disciplines[]'],
                               ['favorite_id', 'phase', 'position', 'exercise_id', 'duration']];
    protected $entityNames = ['Favoriten', 1 => 'FavoritenÜbungen'];
    protected $entityTableNames = ['FAVORITE_DISCIPLINES', 'FAVORITE_EXERCISES', 'FAVORITE_HEADERS'];
    
    protected function insertUploadedEntries($dbConnection) {
        $okay = true;
        $stmt_header = $dbConnection->prepare('INSERT INTO FAVORITE_HEADERS (id, created_by, created_at, description) VALUES (?, ?, ?, ?)');
        $stmt_header->bind_param('isss', $id, $created_by, $created_at, $descr);
        $stmt_dscplns = $dbConnection->prepare('INSERT INTO FAVORITE_DISCIPLINES (favorite_id, discipline_id) VALUES (?,?)');
        $stmt_dscplns->bind_param('is', $id, $discipline_id);

        foreach($this->dataLoad[0] as ['id' => $id, 'created_by' => $created_by, 'created_at' => $created_at,
                                      'description' => $descr, 'disciplines[]' => $disciplines]) {
            try {
                if(!$stmt_header->execute()) {
                    $this->messages[] = "Einfügen der Favoritem-Zeile id=$id, created_by=$created_by, created_at=$created_at, description=$descr ist fehlgeschlagen.";
                    $okay = false;
                } else {
                    foreach($disciplines as $discipline_id) {
                        if(!$stmt_dscplns->execute()) {
                            $this->messages[] = "Hinzufügen der Disziplin id=$discipline_id zu Favorit id=$id ist fehlgeschlagen.";
                            $okay = false;
                        }
                    }
                }
            } catch( mysqli_sql_exception $ex) {
                $error = $ex->getMessage();
                $this->messages[] = "Favorit id=$id in die Datenbank laden ist fehlgeschlagen, Fehler: $error.";
                $okay = false;
            }
        }
        $stmt_dscplns->close();
        $stmt_header->close();
        // 3. insert the exercise-favorite relations
        $stmt_exmap = $dbConnection->prepare('INSERT INTO FAVORITE_EXERCISES (favorite_id, phase, position, exercise_id, duration) VALUES (?,?,?,?,?)');
        $stmt_exmap->bind_param('isisi', $favorite_id, $phase, $position, $exercise_id, $duration);     
        foreach($this->dataLoad[1] as ['favorite_id' => $favorite_id, 'phase' => $phase, 'position' => $position,
                                      'exercise_id' => $exercise_id, 'duration' => $duration]) {
            try {
                if(!$stmt_exmap->execute()) {
                    $messages[] = "Übung $exercise_id zu Favorit $favorite_id (Phase $phase, Pos. $position, Dauer $duration) ist fehlgeschlagen.";
                    $okay = false;
                }          
            } catch( mysqli_sql_exception $ex) {
                $error = $ex->getMessage();
                $messages[] = "Favorit $favorite_id mit Übung $exercise_id in die Datenbank laden ist fehlgeschlagen, Fehler: $error.";
                $okay = false;
            }
        }
        $stmt_exmap->close();
        return $okay;
    }
}
?>
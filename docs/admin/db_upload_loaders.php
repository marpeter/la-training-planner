<?php
namespace LaPlanner;

abstract class DataLoader {
    // override these properties in each concrete class
    protected $headerFields = [];
    protected $entityNames = [];
    protected $entityTableNames = [];
    // default separator is comma - override in subclasses as needed
    protected $separator = ',';
    protected $messages = [];
    protected $fieldMaps = [];
    protected $dataLoad = [];

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
        $dbConnection->beginTransaction();
        // 1. remove current database entries
        $okay = $this->clearCurrentEntries($dbConnection);
        // 2. insert the uploaded disciplines
        $okay = $this->insertUploadedEntries($dbConnection);
        // 3. commit or rollback
        if($okay) {
            $dbConnection->commit();
            $c1 = count($this->dataLoad[0]);
            $messages[] = "Es wurden $c1 {$this->entityNames[0]}en geladen.";
            return 1;
        } else {
            $dbConnection->rollBack();
            $messages[] = "Es wurden keine {$this->entityNames[0]}en geladen.";
            return 0;
        }
    }

    protected function clearCurrentEntries($dbConnection) {
        try {
            foreach($this->entityTableNames as $tableName) {
                $result = $dbConnection->exec("DELETE FROM $tableName");
            }
            return true;
        } catch( \PDOException $ex) {
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
        $stmt = $dbConnection->prepare('INSERT INTO DISCIPLINES ' . 
            '( id,  name,  image) VALUES ' . 
            '(:id, :name, :image)');
        $stmt->bindParam('id', $id, \PDO::PARAM_STR);
        $stmt->bindParam('name', $name, \PDO::PARAM_STR);
        $stmt->bindParam('image', $image, \PDO::PARAM_STR);
        $okay = true;
        foreach($this->dataLoad[0] as ['id' => $id, 'name' => $name, 'image' => $image]) {
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
    protected $separator = ';';

    protected function insertUploadedEntries($dbConnection) {
        $okay = true;
        $stmt_header = $dbConnection->prepare('INSERT INTO EXERCISES ' .
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
        $stmt_dscplns = $dbConnection->prepare('INSERT INTO EXERCISES_DISCIPLINES (exercise_id, discipline_id) VALUES (:id,:discipline_id)');
        $stmt_dscplns->bindParam('id', $id, \PDO::PARAM_STR);
        $stmt_dscplns->bindParam('discipline_id', $discipline_id, \PDO::PARAM_STR);
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
    protected $entityTableNames = ['FAVORITE_DISCIPLINES', 'FAVORITE_EXERCISES', 'FAVORITE_HEADERS'];
    
    protected function insertUploadedEntries($dbConnection) {
        $okay = true;
        $stmt_header = $dbConnection->prepare('INSERT INTO FAVORITE_HEADERS ' . 
            '( id,  created_by,  created_at,  description) VALUES ' . 
            '(:id, :created_by, :created_at, :description)');
        $stmt_header->bindParam('id', $id, \PDO::PARAM_INT);
        $stmt_header->bindParam('created_by', $created_by, \PDO::PARAM_STR);
        $stmt_header->bindParam('created_at', $created_at, \PDO::PARAM_STR);
        $stmt_header->bindParam('description', $descr, \PDO::PARAM_STR);
        $stmt_dscplns = $dbConnection->prepare('INSERT INTO FAVORITE_DISCIPLINES (favorite_id, discipline_id) VALUES (:id,:discipline_id)');
        $stmt_dscplns->bindParam('id', $id, \PDO::PARAM_INT);
        $stmt_dscplns->bindParam('discipline_id', $discipline_id, \PDO::PARAM_STR);
        foreach($this->dataLoad[0] as ['id' => $id, 'created_by' => $created_by, 'created_at' => $created_at,
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
                $this->messages[] = "Einfügen der Favoritem-Zeile id=$id, created_by=$created_by, created_at=$created_at, description=$descr ist fehlgeschlagen, Fehler: $error.";
                $okay = false;
            }
        }
        // 3. insert the exercise-favorite relations
        $stmt_exmap = $dbConnection->prepare('INSERT INTO FAVORITE_EXERCISES ' . 
            '( favorite_id,  phase,  position,  exercise_id,  duration) VALUES ' . 
            '(:favorite_id, :phase, :position, :exercise_id, :duration)');
        $stmt_exmap->bindParam(':favorite_id', $favorite_id, \PDO::PARAM_INT);
        $stmt_exmap->bindParam(':phase', $phase, \PDO::PARAM_STR);
        $stmt_exmap->bindParam(':position', $position, \PDO::PARAM_INT);
        $stmt_exmap->bindParam(':exercise_id', $exercise_id, \PDO::PARAM_STR);
        $stmt_exmap->bindParam(':duration', $duration, \PDO::PARAM_INT);
        foreach($this->dataLoad[1] as ['favorite_id' => $favorite_id, 'phase' => $phase, 'position' => $position,
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
?>
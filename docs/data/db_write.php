<?php
namespace LaPlanner;
include('db_common.php'); 

abstract class DataSaver {
    protected $ENTITY = "";
    protected $dbConnection;

    /**
     * inserts multiple entries into the DB in a bulk operation.
     * @param array $data array of arrays of entries to insert.
     *              $data[0] contains the array of "header" entries to insert,
     *              $data[1...] contain entries for depending tables (if applicable)
     * @param $dbConnection active DB connection to use for the bulk insert
     * @return array of message strings indicating errors during insert
     */
    abstract public function createEntityBulk(array $data, $dbConnection): array;
    /**
     * inserts a single entry into the DB.
     * @param array $data associative array representing the entry to insert
     * @return void
     * @throws \PDOException on error
     */
    abstract protected function createEntity(array $data): void;
    /** 
     * updates a single entry in the DB.
     * @param array $data associative array representing the entry to update
     * @return void
     * @throws \PDOException on error
     */
    abstract protected function updateEntity(array $data): void;
    /** 
     * deletes a single entry from the DB.
     * @param mixed $id identifier of the entry to delete
     * @return void
     * @throws \PDOException on error
     */
    abstract protected function deleteEntity($id): void;

    public final function create($data): array {
        return $this->doAction($data, 'createEntity', 'Anlegen');
    }
    public final function update($data): array {
        return $this->doAction($data, 'updateEntity', 'Ändern');
    }
    public final function delete($id): array {
        return $this->doAction($id, 'deleteEntity', 'Löschen');
    }
    private function doAction($data, $action, $actionName): array {
        $result = "";
        try {
            $this->dbConnection = connectDB();
            $this->dbConnection->beginTransaction();
            $this->$action($data);
            $this->dbConnection->commit();
            $result =  [
                'success' => true,
                'message' => $data,
            ];
        } catch (\PDOException $ex) {
            $this->dbConnection->rollBack();
            return [
                'success' => false,
                'message' => "Fehler beim {$actionName} der {$this->ENTITY}: " . $ex->getMessage(),
            ];
        } finally {
            $this->dbConnection = null;
        }
        return $result;
    }
}

class DisciplineSaver extends DataSaver {
    protected $ENTITY = "Disziplin";

    public function createEntityBulk(array $disciplines, $dbConnection): array {
        $messages = [];
        $this->dbConnection = $dbConnection;
        foreach($disciplines[0] as $discipline) {
            try {
                $this->createEntity($discipline);
            } catch( \PDOException $ex) {
                $messages[] =  $$ex->getMessage();
            }
        }
        return $messages;
    }

    protected function createEntity(array $discipline): void {
        try {
            $stmt = $this->dbConnection->prepare('INSERT INTO DISCIPLINES ' . 
                '( id,  name,  image) VALUES ' . 
                '(:id, :name, :image)');
            $stmt->bindParam('id', $discipline['id'], \PDO::PARAM_STR);
            $stmt->bindParam('name', $discipline['name'], \PDO::PARAM_STR);
            $stmt->bindParam('image', $discipline['image'], \PDO::PARAM_STR);
            $stmt->execute();
        } catch( \PDOException $ex) {
            $error = $ex->getMessage();
            throw new \PDOException("Einfügen der Disziplin-Zeile " .
                "{$discipline['id']}, {$discipline['name']}, " . 
                "{$discipline['image']} ist fehlgeschlagen, Fehler: $error.");
        }
    }

    protected function updateEntity(array $discipline): void {
        throw new \PDOException("Not implemented because not needed so far.");
        // Not needed so far
    }
    
    protected function deleteEntity($disciplineId): void {
        throw new \PDOException("Not implemented because not needed so far.");
        // Not needed so far
    }
}

class ExerciseSaver extends DataSaver {
    protected $ENTITY = "Übung";

    public function createEntityBulk(array $exercises, $dbConnection): array {
        $messages = [];
        $this->dbConnection = $dbConnection;
        foreach($exercises[0] as $exercise) {
            try {
                // reconvert details array to string because it was split during loading
                // but is not stored in a separate table on the DB
                $exercise['details'] = implode(':',$exercise['details']);
                $this->createEntity($exercise);
            } catch( \PDOException $ex) {
                $messages[] =  $$ex->getMessage();
            }
        }
        return $messages;
    }

    protected function createEntity(array $exercise): void {
        $this->convertPhaseFlags($exercise);     
        try {
            $stmt = $this->dbConnection->prepare(
                'INSERT INTO EXERCISES ' . 
                '(id, name, warmup, runabc, mainex, ending, durationmin, durationmax, material, repeats, details) VALUES ' .
                '(:id, :name, :warmup, :runabc, :mainex, :ending, :durationmin, :durationmax, :material, :repeats, :details)');
            $this->bindUpsertParams($stmt, $exercise);
            $stmt->execute();
            $stmt = null;
            $this->insertDependants($exercise);
        } catch (\PDOException $ex) {
            throw new \PDOException('Fehler beim Erstellen der Übung: ' . $ex->getMessage());
        }
    }

    protected function updateEntity(array $exercise): void {
        $this->convertPhaseFlags($exercise);
        try {
            $stmt = $this->dbConnection->prepare(
                'UPDATE EXERCISES SET name=:name, warmup=:warmup, runabc=:runabc, mainex=:mainex, ending=:ending, durationmin=:durationmin, ' . 
                'durationmax=:durationmax, material=:material, repeats=:repeats, details=:details WHERE id = :id');
            $this->bindUpsertParams($stmt, $exercise);
            $stmt->execute();
            $stmt = null;
            $this->deleteDependants($exercise['id']);
            $this->insertDependants($exercise);
        } catch (\PDOException $ex) {
            throw new \PDOException('Fehler beim Ändern der Übung: ' . $ex->getMessage());
        }
    }

    protected function deleteEntity($exerciseId): void {
        try {
            $stmt = $this->dbConnection->prepare('DELETE FROM EXERCISES WHERE id = :id');
            $stmt->bindParam('id', $exerciseId, \PDO::PARAM_STR);
            $stmt->execute();
            $this->deleteDependants($exerciseId,$dbConnection);
            $stmt = $this->dbConnection->prepare('DELETE FROM FAVORITE_EXERCISES WHERE exercise_id=:id');
            $stmt->bindParam('id', $exerciseId, \PDO::PARAM_STR);
            $stmt->execute();
        } catch (\PDOException $ex) {
            throw new \PDOException('Fehler beim Löschen der Übung: ' . $ex->getMessage());
        }
    }

    private function bindUpsertParams($stmt, $exercise): void {
        $stmt->bindParam('id', $exercise['id'], \PDO::PARAM_STR);
        $stmt->bindParam('name', $exercise['name'], \PDO::PARAM_STR);
        $stmt->bindParam('warmup', $exercise['warmup'], \PDO::PARAM_INT);
        $stmt->bindParam('runabc', $exercise['runabc'], \PDO::PARAM_INT);
        $stmt->bindParam('mainex', $exercise['mainex'], \PDO::PARAM_INT);
        $stmt->bindParam('ending', $exercise['ending'], \PDO::PARAM_INT);
        $stmt->bindParam('durationmin', $exercise['durationmin'], \PDO::PARAM_INT);
        $stmt->bindParam('durationmax', $exercise['durationmax'], \PDO::PARAM_INT);
        $stmt->bindParam('material', $exercise['material'], \PDO::PARAM_STR);
        $stmt->bindParam('repeats', $exercise['repeats'], \PDO::PARAM_STR);
        $stmt->bindParam('details', $exercise['details'], \PDO::PARAM_STR);
    }

    private function convertPhaseFlags(array &$exercise): void {
        $exercise['warmup'] = $exercise['warmup']=="true" ? 1 : 0;
        $exercise['runabc'] = $exercise['runabc']=="true" ? 1 : 0;
        $exercise['mainex'] = $exercise['mainex']=="true" ? 1 : 0;
        $exercise['ending'] = $exercise['ending']=="true" ? 1 : 0;
    }

    private function deleteDependants($exerciseId): void {
        $stmt = $this->dbConnection->prepare('DELETE FROM EXERCISES_DISCIPLINES WHERE exercise_id=:id');
        $stmt->bindParam('id', $exerciseId, \PDO::PARAM_STR);
        $stmt->execute();
        // note that FAVORITE_EXERCISES are NOT deleted to prevent them from being deleted in the update case
        //      because the exercise data would not include update information for FAVORITE_EXERCISES
    }

    private function insertDependants(array $exercise): void {
        try {
            $stmt = $this->dbConnection->prepare('INSERT INTO EXERCISES_DISCIPLINES (exercise_id, discipline_id) '
                . 'VALUES (:exercise_id, :discipline_id)');
            $stmt->bindParam('exercise_id', $exercise['id'], \PDO::PARAM_STR);
            $stmt->bindParam('discipline_id', $discipline_id, \PDO::PARAM_STR);
            foreach($exercise['disciplines'] as $discipline_id){
                $stmt->execute();
            }
        } catch (\PDOException $ex) {
            throw new \PDOException('Fehler beim Erstellen der Disziplinen der Übung: ' . $ex->getMessage());   
        }
    }
}

class FavoriteSaver extends DataSaver {
    protected $ENTITY = "Favoriten";

    public function createEntityBulk($favorites, $dbConnection): array {
        $messages = [];
        $this->dbConnection = $dbConnection;
        // Note that this method expects $favorites to be an array of two arrays:
        //   - [0] = array of favorite headers including associated disciplines, but
        //             without mapping favorite exercises to phases
        //   - [1] = array of favorite exercises
        foreach($favorites[0] as $favorite) {
            try {
                foreach(['warmup', 'mainex', 'ending'] as $phase) {
                    $favorite[$phase] = [];
                }
                $this->createEntity($favorite);
            } catch( \PDOException $ex) {
                $messages[] =  $ex->getMessage();
            }
        }
        $fex_messages = $this->insertFavoriteExercisesBulk($favorites[1]);
        return array_merge($messages, $fex_messages);
    }

    private function insertFavoriteExercisesBulk(array $favoriteExercises): array {
        $messages = [];
        $stmt = $this->dbConnection->prepare('INSERT INTO FAVORITE_EXERCISES ' . 
            '( favorite_id,  phase,  position,  exercise_id,  duration) VALUES ' . 
            '(:favorite_id, :phase, :position, :exercise_id, :duration)');
        $stmt->bindParam(':favorite_id', $favorite_id, \PDO::PARAM_INT);
        $stmt->bindParam(':phase', $phase, \PDO::PARAM_STR);
        $stmt->bindParam(':position', $position, \PDO::PARAM_INT);
        $stmt->bindParam(':exercise_id', $exercise_id, \PDO::PARAM_STR);
        $stmt->bindParam(':duration', $duration, \PDO::PARAM_INT);
        foreach($favoriteExercises as ['favorite_id' => $favorite_id,
                'phase' => $phase, 'position' => $position,
                'exercise_id' => $exercise_id, 'duration' => $duration]) {
            try {
                $stmt->execute();
            } catch( \PDOException $ex) {
                $error = $ex->getMessage();
                $messages[] = "Übung $exercise_id zu Favorit $favorite_id (Phase $phase, Pos. $position, Dauer $duration) ist fehlgeschlagen, Fehler: $error.";
            }
        }
        return $messages;
    }

    protected function createEntity(array $favorite): void {
        try {
            $stmt = $this->dbConnection->prepare('INSERT INTO FAVORITE_HEADERS (id, created_by, description) VALUES ' . 
                '(:id, :created_by, :description)');
            $stmt->bindParam('id', $favorite['id'], \PDO::PARAM_INT);
            $stmt->bindParam('created_by', $favorite['created_by'], \PDO::PARAM_STR);
            $stmt->bindParam('description', $favorite['description'], \PDO::PARAM_STR);
            $stmt->execute();
            $this->insertDependants($favorite);
        } catch (\PDOException $ex) {
            throw new \PDOException('Fehler beim Erstellen des Favoriten: ' . $ex->getMessage());   
        }
    }

    protected function updateEntity(array $favorite): void {
        try {
            $stmt = $this->dbConnection->prepare('UPDATE FAVORITE_HEADERS SET description = :description WHERE id = :id');
            $stmt->bindParam('id', $favorite['id'], \PDO::PARAM_INT);
            $stmt->bindParam('description', $favorite['description'], \PDO::PARAM_STR);
            $this->deleteDependants($favorite['id']);
            $this->insertDependants($favorite);
        } catch (\PDOException $ex) {
            throw new \PDOException('Fehler beim Ändern des Favoriten: ' . $ex->getMessage());   
        }
    }

    protected function deleteEntity($favoriteId): void {
        try {
            $stmt = $this->dbConnection->prepare('DELETE FROM FAVORITE_HEADERS WHERE id = :id');
            $stmt->bindParam('id', $favoriteId, \PDO::PARAM_INT);
            $stmt->execute();
            $this->deleteDependants($favoriteId);
        } catch (\PDOException $ex) {
            throw new \PDOException('Fehler beim Laden des Favoriten zum Löschen: ' . $ex->getMessage());
        }
    }

    private function insertDependants($favorite): void {
        try {
            $stmt = $this->dbConnection->prepare('INSERT INTO FAVORITE_DISCIPLINES (favorite_id, discipline_id) VALUES ' . 
                '(:favorite_id, :discipline_id)');
            $stmt->bindParam('favorite_id', $favorite['id'], \PDO::PARAM_INT);
            $stmt->bindParam('discipline_id', $discipline_id, \PDO::PARAM_STR);
            // When called from createEntityBulk, $discipline is just a string (id)
            // When called from updateEntity (from the JavaScript model),
            //                  $discipline is an array with fields id, name and image
            foreach($favorite['disciplines'] as $discipline) {
                $discipline_id = is_array($discipline) ? $discipline['id'] : $discipline;
                $stmt->execute();
            }
            $stmt = $this->dbConnection->prepare('INSERT INTO FAVORITE_EXERCISES ' . 
                '( favorite_id,  exercise_id,  phase,  position,  duration) VALUES ' . 
                '(:favorite_id, :exercise_id, :phase, :position, :duration)');
            $stmt->bindParam('favorite_id', $favorite['id'], \PDO::PARAM_INT);
            $stmt->bindParam('exercise_id', $exercise['id'], \PDO::PARAM_STR);
            $stmt->bindParam('phase', $phase, \PDO::PARAM_STR);
            $stmt->bindParam('position', $position, \PDO::PARAM_INT);
            $stmt->bindParam('duration', $exercise['duration'], \PDO::PARAM_INT);
            foreach(['warmup', 'mainex', 'ending'] as $phase) {
                $position = 1;
                foreach($favorite[$phase] as $exercise) {
                    if (isset($exercise['position'])) {
                        $position = $exercise['position'];
                    }
                    $stmt->execute();
                    $position++;
                }
            }
        } catch (\PDOException $ex) {
            throw new \PDOException('Fehler beim Erstellen der Disziplinen des Favoriten: ' . $ex->getMessage());
        }
    }

    private function deleteDependants($favoriteId): void {
        try {   
            $stmt = $this->dbConnection->prepare('DELETE FROM FAVORITE_DISCIPLINES WHERE favorite_id = :id');
            $stmt->bindParam('id', $favorite['id'], \PDO::PARAM_INT);
            $stmt->execute();
        } catch (\PDOException $ex) {
            throw new \PDOException('Fehler beim Löschen der Disziplinen des Favoriten: ' . $ex->getMessage());
        }
        try {   
            $stmt = $this->dbConnection->prepare('DELETE FROM FAVORITE_EXERCISES WHERE favorite_id = :id');
            $stmt->bindParam('id', $favorite['id'], \PDO::PARAM_INT);
            $stmt->execute();
        } catch (\PDOException $ex) {
            throw new \PDOException('Fehler beim Löschen der Übungen im Favoriten: ' . $ex->getMessage());
        }       
    }
}
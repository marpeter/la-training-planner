<?php
namespace LaPlanner;
require 'db_common.php'; 

abstract class DataSaver {
    protected $ENTITY = "";
    protected ?\PDO $dbConnection = null;

    /**
     * inserts multiple entries into the DB in a bulk operation.
     * @param array $data array of arrays of entries to insert.
     *              $data[0] contains the array of "header" entries to insert,
     *              $data[1...] contain entries for depending tables (if applicable)
     * @param ?\PDO $dbConnection active DB connection to use for the bulk insert
     * @return array of message strings indicating errors during insert
     */
    abstract public function createEntityBulk(array $data, ?\PDO $dbConnection): array;

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
    abstract protected function deleteEntity(string $id): void;

    /** 
     * checks if a single entry would be consistent in the DB and sanitizes it.
     * @param array $data associative array representing the entry
     * @return void
     * @throws \PDOException on error
     */
    abstract protected function sanitizeAndValidateEntity(array &$data): void;
    
    public final function create(array $data): array {
        return $this->doAction($data, 'createEntity', 'Anlegen');
    }
    public final function update(array $data): array {
        return $this->doAction($data, 'updateEntity', 'Ändern');
    }
    public final function delete(string $id): array {
        return $this->doAction($id, 'deleteEntity', 'Löschen');
    }
    private function doAction($data, string $action, string $actionName): array {
        $result = "";
        try {
            if( $action != 'deleteEntity') {
                $this->sanitizeAndValidateEntity($data);
            }
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
    use \TnFAT\Planner\DisciplineTable;

    protected $ENTITY = "Disziplin";

    public function createEntityBulk(array $disciplines, ?\PDO $dbConnection): array {
        $messages = [];
        if( $dbConnection ) $this->dbConnection = $dbConnection;
        $stmt = $this->dbConnection->prepare('INSERT INTO ' . self::HEADER_TABLE . 
            ' ( id,  name,  image) VALUES' . 
            ' (:id, :name, :image)');
        $stmt->bindParam('id', $disciplineId, \PDO::PARAM_STR);
        $stmt->bindParam('name', $disciplineName, \PDO::PARAM_STR);
        $stmt->bindParam('image', $disciplineImage, \PDO::PARAM_STR);
        foreach($disciplines[0] as $discipline) {
            try {
                $this->sanitizeAndValidateEntity($discipline);
                $disciplineId = $discipline['id'];
                $disciplineName = $discipline['name'];
                $disciplineImage = $discipline['image'];
                $stmt->execute();
            } catch( \PDOException $ex) {
                $error = $ex->getMessage();
                $messages[] =  "Einfügen der Disziplin-Zeile $disciplineId," .
                    " $disciplineName, $disciplineImage ist fehlgeschlagen, " . 
                    " Fehler: $error.";
            }
        }
        return $messages;
    }

    protected function createEntity(array $discipline): void {
        $this->createEntityBulk([$discipline], $this->dbConnection);
        if( !is_empty($this->messages)) {
            throw new \PDOException(array_last($this->messages));
        }
    }

    protected function updateEntity(array $discipline): void {
        throw new \PDOException("Not implemented because not needed so far.");
        // Not needed so far
    }
    
    protected function deleteEntity(string $disciplineId): void {
        throw new \PDOException("Not implemented because not needed so far.");
        // Not needed so far
    }

    protected function sanitizeAndValidateEntity(array &$discipline): void {
        $discipline['id'] = strip_tags($discipline['id']);
        $discipline['name'] = strip_tags($discipline['name']);
        if($discipline['name'] === '') {
            throw new \PDOException('Der Name einer Disziplin darf nicht leer sein.');
        }
        $discipline['image'] = strip_tags($discipline['image']);
    }
}

class FavoriteSaver extends DataSaver {
    use \TnFAT\Planner\FavoriteTable;
    protected $ENTITY = "Favoriten";

    public function createEntityBulk(array $favorites, ?\PDO $dbConnection): array {
        $messages = [];
        if( $dbConnection ) $this->dbConnection = $dbConnection;
        // Note that this method expects $favorites to be an array of two arrays:
        //   - [0] = array of favorite headers including associated disciplines, but
        //             without mapping favorite exercises to phases
        //   - [1] = array of favorite exercises

        // sort the favorite exercise by favorite_id to avoid nested loop
        usort($favorites[1], function($a, $b) {
            return $a['favorite_id'] - $b['favorite_id'];
        });
        $lastFavoriteExerciseId = 0;
        foreach($favorites[0] as $favorite) {
            try {
                foreach(['warmup', 'mainex', 'ending'] as $phase) {
                    $favorite[$phase] = [];
                }
                // assign favorite exercises to phases
                while( $lastFavoriteExerciseId < count($favorites[1]) &&
                    $favorites[1][$lastFavoriteExerciseId]['favorite_id'] == $favorite['id']) {
                    $fex = $favorites[1][$lastFavoriteExerciseId];
                    $favorite[$fex['phase']][] = [
                        'id' => $fex['exercise_id'],
                        'duration' => $fex['duration'],
                        'position' => $fex['position'],
                    ];
                    $lastFavoriteExerciseId++;
                }
                $this->sanitizeAndValidateEntity($favorite);
                $this->createEntity($favorite);
            } catch( \PDOException $ex) {
                $messages[] =  $ex->getMessage();
            }
        }
        return $messages;
    }

    protected function createEntity(array $favorite): void {
        try {
            $stmt = $this->dbConnection->prepare('INSERT INTO ' . self::HEADER_TABLE .
                ' (id, created_by, description) VALUES ' . 
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
            $stmt = $this->dbConnection->prepare('UPDATE ' . self::HEADER_TABLE . 
                ' SET description = :description WHERE id = :id');
            $stmt->bindParam('id', $favorite['id'], \PDO::PARAM_INT);
            $stmt->bindParam('description', $favorite['description'], \PDO::PARAM_STR);
            $this->deleteDependants($favorite['id']);
            $this->insertDependants($favorite);
        } catch (\PDOException $ex) {
            throw new \PDOException('Fehler beim Ändern des Favoriten: ' . $ex->getMessage());   
        }
    }

    protected function deleteEntity(string $favoriteId): void {
        try {
            $stmt = $this->dbConnection->prepare('DELETE FROM ' .
                self::HEADER_TABLE . ' WHERE id = :id');
            $stmt->bindParam('id', $favoriteId, \PDO::PARAM_INT);
            $stmt->execute();
            $this->deleteDependants($favoriteId);
        } catch (\PDOException $ex) {
            throw new \PDOException('Fehler beim Laden des Favoriten zum Löschen: ' . $ex->getMessage());
        }
    }

    protected function sanitizeAndValidateEntity(array &$favorite): void {
        if( !is_array($favorite['disciplines']) || count($favorite['disciplines']) == 0 ) {
            throw new \PDOException('Jeder Favorit muss mindestens einer Disziplin zugeordnet sein.');
        }
        $favorite['id'] = strip_tags($favorite['id']);
        $favorite['created_by'] = strip_tags($favorite['created_by']);
        $favorite['description'] = strip_tags($favorite['description'], ALLOWED_TAGS);
        foreach(['warmup', 'mainex', 'ending'] as $phase) {
            foreach($favorite[$phase] as $exercise) {
                $exercise['id'] = strip_tags($exercise['id']);
                if( $exercise['id'] == '' ) {
                    throw new \PDOException('Jede Favoritenübung muss eine Übungs-ID haben.');
                }
                if( isset($exercise['position']) && (!is_numeric($exercise['position']) || $exercise['position'] <= 0 )) {
                    throw new \PDOException("Ungültige Position {$exercise['position']} für Favoritenübung.");
                }
                if( !is_numeric($exercise['duration']) || $exercise['duration'] < 0 ) {
                    throw new \PDOException("Ungültige Dauer '{$exercise['duration']}' für Favoritenübung.");
                }
                
            }
        }
    }

    private function insertDependants($favorite): void {
        try {
            $stmt = $this->dbConnection->prepare('INSERT INTO ' . 
                self::LINK_DISCIPLINES_TABLE . 
                ' (favorite_id, discipline_id) VALUES ' . 
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
        } catch (\PDOException $ex) {
            throw new \PDOException('Fehler beim Erstellen der Disziplinen des Favoriten: ' . $ex->getMessage());
        }
        try {
            $stmt = $this->dbConnection->prepare('INSERT INTO ' .
                self::LINK_EXERCISES_TABLE .
                ' (favorite_id,  exercise_id,  phase,  position,  duration) VALUES ' . 
                '(:favorite_id, :exercise_id, :phase, :position, :duration)');
            $stmt->bindParam('favorite_id', $favorite['id'], \PDO::PARAM_INT);
            $stmt->bindParam('phase', $phase, \PDO::PARAM_STR);
            $stmt->bindParam('position', $position, \PDO::PARAM_INT);
            foreach(['warmup', 'mainex', 'ending'] as $phase) {
                $position = 1;
                foreach($favorite[$phase] as $exercise) {
                    $stmt->bindParam('exercise_id', $exercise['id'], \PDO::PARAM_STR);
                    $stmt->bindParam('duration', $exercise['duration'], \PDO::PARAM_INT);
                    if (isset($exercise['position'])) {
                        $position = $exercise['position'];
                    }
                    $stmt->execute();
                    $position++;
                }
            }
        } catch (\PDOException $ex) {
            throw new \PDOException('Fehler beim Erstellen der Übungen des Favoriten: ' . $ex->getMessage());
        }
    }

    private function deleteDependants(string $favoriteId): void {
        try {   
            $stmt = $this->dbConnection->prepare('DELETE FROM ' . 
                self::LINK_DISCIPLINES_TABLE . ' WHERE favorite_id = :id');
            $stmt->bindParam('id', $favoriteId, \PDO::PARAM_INT);
            $stmt->execute();
        } catch (\PDOException $ex) {
            throw new \PDOException('Fehler beim Löschen der Disziplinen des Favoriten: ' . $ex->getMessage());
        }
        try {   
            $stmt = $this->dbConnection->prepare('DELETE FROM ' .
                self::LINK_EXERCISES_TABLE . ' WHERE favorite_id = :id');
            $stmt->bindParam('id', $favoriteId, \PDO::PARAM_INT);
            $stmt->execute();
        } catch (\PDOException $ex) {
            throw new \PDOException('Fehler beim Löschen der Übungen des Favoriten: ' . $ex->getMessage());
        }       
    }
}
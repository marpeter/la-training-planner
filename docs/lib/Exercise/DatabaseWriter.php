<?php
namespace TnFAT\Planner\Exercise;

class DatabaseWriter extends \LaPlanner\DataSaver {
    use DatabaseTable;
    protected $ENTITY = "Übung";

    public function createEntityBulk(array $exercises, ?\PDO $dbConnection): array {
        $messages = [];
        if( $dbConnection ) $this->dbConnection = $dbConnection;
        foreach($exercises[0] as $exercise) {
            try {
                // reconvert details array to string because it was split during loading
                // but is not stored in a separate table on the DB
                $exercise['details'] = implode(':',$exercise['details']);
                $this->sanitizeAndValidateEntity($exercise);
                $this->createEntity($exercise);
            } catch( \PDOException $ex) {
                $messages[] =  $ex->getMessage();
            }
        }
        return $messages;
    }

    protected function createEntity(array $exercise): void {
        try { 
            $stmt = $this->dbConnection->prepare(
                'INSERT INTO ' . self::HEADER_TABLE . 
                ' (id, name, warmup, runabc, mainex, ending, durationmin, durationmax, material, repeats, details) VALUES ' .
                '(:id, :name, :warmup, :runabc, :mainex, :ending, :durationmin, :durationmax, :material, :repeats, :details)');
            $this->bindUpsertParams($stmt, $exercise);
            $stmt->execute();
            $stmt = null;
            $this->insertDependants($exercise);
        } catch (\PDOException $ex) {
            throw new \PDOException('Fehler beim Erstellen der Übung ' . 
                $exercise['id'] . ' : ' . $ex->getMessage());
        }
    }

    protected function updateEntity(array $exercise): void {
        try {
            $stmt = $this->dbConnection->prepare(
                'UPDATE ' . self::HEADER_TABLE . ' SET name=:name, ' . 
                'warmup=:warmup, runabc=:runabc, mainex=:mainex, ending=:ending, ' . 
                'durationmin=:durationmin, durationmax=:durationmax, ' . 
                'material=:material, repeats=:repeats, details=:details ' . 
                'WHERE id = :id');
            $this->bindUpsertParams($stmt, $exercise);
            $stmt->execute();
            $stmt = null;
            $this->deleteDependants($exercise['id']);
            $this->insertDependants($exercise);
        } catch (\PDOException $ex) {
            throw new \PDOException('Fehler beim Ändern der Übung: ' . $ex->getMessage());
        }
    }

    protected function deleteEntity(string $exerciseId): void {
        try {
            $stmt = $this->dbConnection->prepare('DELETE FROM ' .
                 self::HEADER_TABLE . ' WHERE id = :id');
            $stmt->bindParam('id', $exerciseId, \PDO::PARAM_STR);
            $stmt->execute();
            $this->deleteDependants($exerciseId,$dbConnection);
            $stmt = $this->dbConnection->prepare('DELETE FROM ' .
                self::LINK_DISCIPLINES_TABLE . '  WHERE exercise_id=:id');
            $stmt->bindParam('id', $exerciseId, \PDO::PARAM_STR);
            $stmt->execute();
        } catch (\PDOException $ex) {
            throw new \PDOException('Fehler beim Löschen der Übung: ' . $ex->getMessage());
        }
    }

    protected function sanitizeAndValidateEntity(array &$exercise): void {
        if( !is_array($exercise['disciplines']) || count($exercise['disciplines']) == 0 ) {
            throw new \PDOException('Jede Übung muss mindestens einer Disziplin zugeordnet sein.');
        }
        // No check of discipline IDs - rely on foreign key constraint of the DB
        if( !is_numeric($exercise['durationmin']) || !is_numeric($exercise['durationmax']) ) {
            throw new \PDOException('Die Dauerangaben müssen numerisch sein.');
        }
        if( $exercise['durationmin'] < 0 || $exercise['durationmax'] < 0 ) {
            throw new \PDOException('Die Dauerangaben dürfen nicht negativ sein.');
        }
        if( $exercise['durationmin'] > $exercise['durationmax'] ) {
            throw new \PDOException('Die minimale Dauer darf nicht größer als die maximale Dauer sein.');
        }
        $exercise['id'] = strip_tags($exercise['id']);
        $exercise['name'] = strip_tags($exercise['name'], \LaPlanner\ALLOWED_TAGS);
        if($exercise['name'] == '') {
            throw new \PDOException('Der Name der Übung darf nicht leer sein.');
        }
        $exercise['material'] = strip_tags($exercise['material'], \LaPlanner\ALLOWED_TAGS);
        $exercise['repeats'] = strip_tags($exercise['repeats'], \LaPlanner\ALLOWED_TAGS);
        $exercise['details'] = strip_tags($exercise['details'], \LaPlanner\ALLOWED_TAGS);
        foreach(['warmup', 'runabc','mainex', 'ending'] as $phase) {
            $exercise[$phase] = filter_var($exercise[$phase], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if( is_null($exercise[$phase]) ) {
                throw new \PDOException("Ungültiger Wert für Phasenkennzeichen '$phase'.");
            } else {
                // convert to boolean
                $exercise[$phase] = $exercise[$phase] ? 1 : 0;
            }
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

    private function deleteDependants($exerciseId): void {
        $stmt = $this->dbConnection->prepare('DELETE FROM ' .
            self::LINK_DISCIPLINES_TABLE . ' WHERE exercise_id=:id');
        $stmt->bindParam('id', $exerciseId, \PDO::PARAM_STR);
        $stmt->execute();
        // note that FAVORITE_EXERCISES are NOT deleted to prevent them from being deleted in the update case
        //      because the exercise data would not include update information for FAVORITE_EXERCISES
    }

    private function insertDependants(array $exercise): void {
        try {
            $stmt = $this->dbConnection->prepare('INSERT INTO '
                . self::LINK_DISCIPLINES_TABLE
                . ' (exercise_id, discipline_id) VALUES '
                . '(:exercise_id, :discipline_id)');
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
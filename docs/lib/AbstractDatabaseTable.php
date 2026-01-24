<?php
namespace TnFAT\Planner;

abstract class AbstractDatabaseTable {
    protected $ENTITY_LOCALIZED = ""; // Human-readable entity name, e.g., "Übung"
    protected string $entity = ""; // machine-readable entity name, e.g., "exercise"
    protected ?\PDO $dbConnection = null;

    /**
     * reads all or a single entry from the DB.
     * @param ?string $id identifier of the entry to read, or null to read all entries
     * @return array of associative arrays representing the entries read
     * @throws \PDOException on error
     */
    public function read(?string $id=null): array {
        $data = [];
        $dbConnection = \LaPlanner\connectDB();
        if( $id === null ) { // read all entries
            foreach ($this->getTableNames() as $tableName) {
                $sql = "SELECT * FROM $tableName";
                $result = $dbConnection->query($sql);
                if ($result) {
                    $data[$tableName] = $result->fetchAll();
                } else {
                    $data[$tableName] = [];
                }
                $result = null;
            }
        } else { // read single entry
            $tableNames = $this->getTableNames();
            $tableName = array_shift($tableNames);
            $stmt = $dbConnection->prepare("SELECT * FROM $tableName WHERE id = :id");
            $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
            if($stmt->execute()) {
                $data[$tableName] = $stmt->fetchAll();
                foreach ($tableNames as $tableName) {
                    $stmt = $dbConnection->prepare("SELECT * FROM $tableName WHERE {$this->entity}_id = :id");
                    $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
                    if($stmt->execute()) {
                        $data[$tableName] = $stmt->fetchAll();
                    } else {
                        $data[$tableName] = [];
                    }
                }
            } else {
                $data[$tableName] = [];
            }
        }
        $dbConnection = null;
        return $data;
    }

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

    /**
     * @return array of the names of the tables associated with this entity.
     */
    // can be added to each concrete class by using the appropriate trait
    abstract protected function getTableNames(): array;
    
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
            $this->dbConnection = \LaPlanner\connectDB();
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
                'message' => "Fehler beim {$actionName} der {$this->ENTITY_LOCALIZED}: " . $ex->getMessage(),
            ];
        } finally {
            $this->dbConnection = null;
        }
        return $result;
    }
}
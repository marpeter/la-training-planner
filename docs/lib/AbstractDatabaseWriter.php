<?php
namespace TnFAT\Planner;

abstract class AbstractDatabaseWriter {
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
                'message' => "Fehler beim {$actionName} der {$this->ENTITY}: " . $ex->getMessage(),
            ];
        } finally {
            $this->dbConnection = null;
        }
        return $result;
    }
}
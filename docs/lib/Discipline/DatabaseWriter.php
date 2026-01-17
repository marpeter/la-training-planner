<?php
namespace TnFAT\Planner\Discipline;

class DatabaseWriter extends \LaPlanner\DataSaver {
    use DatabaseTable;

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
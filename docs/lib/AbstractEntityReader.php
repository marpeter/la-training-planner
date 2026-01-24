<?php
namespace TnFAT\Planner;

abstract class AbstractEntityReader {
    protected $data = [];
    protected ?string $entity = null;

    protected function readFromDb(?string $id): void {
        $dbConnection = \LaPlanner\connectDB();
        if( $id === null ) { // read all entries
            foreach ($this->getTableNames() as $tableName) {
                $sql = "SELECT * FROM $tableName";
                $result = $dbConnection->query($sql);
                if ($result) {
                    $this->data[$tableName] = $result->fetchAll();
                } else {
                    $this->data[$tableName] = [];
                }
                $result = null;
            }
        } else { // read single entry
            $tableNames = $this->getTableNames();
            $tableName = array_shift($tableNames);
            $entity = rtrim($tableName, 's'); // crude singularization // TODO: does not work for favorites
            $stmt = $dbConnection->prepare("SELECT * FROM $tableName WHERE id = :id");
            $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
            if($stmt->execute()) {
                $this->data[$tableName] = $stmt->fetchAll();
                foreach ($tableNames as $tableName) {
                    $stmt = $dbConnection->prepare("SELECT * FROM $tableName WHERE {$this->entity}_id = :id");
                    $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
                    if($stmt->execute()) {
                        $this->data[$tableName] = $stmt->fetchAll();
                    } else {
                        $this->data[$tableName] = [];
                    }
                }
            } else {
                $this->data[$tableName] = [];
            }
        }
        $dbConnection = null;
    }
    protected function setHeader(): void {
        header('Content-Type: application/json');
    }
    // override @format in each concrete class to convert $this->data as returned from the
    // database into the desired format (JSON or CSV)
    abstract protected function format(): string;

    // @getTableNames is added to each concrete class by using the appropriate trait
    abstract protected function getTableNames(): array;

    public static function getReader(string $entity, string $format="json"): AbstractEntityReader {
        if( $format === '' || $format === 'json' ) {
            $readerClass = "\\TnFAT\\Planner\\$entity\\ToJsonReader";
        } else if( $format === 'csv' ) {
            $readerClass = "\\TnFAT\\Planner\\$entity\\ToCsvReader";
        } else {
            return [
                'success' => false,
                'message' => 'You must specify a valid format instead of: ' . htmlspecialchars($format),
            ];
        }
        $reader = new $readerClass();
        $reader->entity = strtolower($entity);
        return $reader;
    }

    public function read(?string $id): string {
        $this->readFromDb($id);
        $this->setHeader();
        return $this->format();
    }
  }
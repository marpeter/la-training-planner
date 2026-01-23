<?php
namespace TnFAT\Planner;

abstract class AbstractEntityReader {
    protected $data = null;

    protected function readFromDb(): void {
        $dbConnection = \LaPlanner\connectDB();
        foreach ($this->getTableNames() as $tableName) {
            $sql = "SELECT * FROM $tableName";
            $result = $dbConnection->query($sql);
            if ($result) {
                $this->data[$tableName] = $result->fetchAll();
            } else {
                $this->data[$tableName] = [];
            } 
        }
        $result = null;
        $dbConnection = null;
    }
    protected function setHeader(): void {
        header('Content-Type: application/json');
    }
    // override @format in each concrete class to convert $this->data as returned from the
    // database into the desired format
    abstract protected function format(): string;
    
    // @getTableNames is added to each concrete class by using the appropriate trait below
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
        return new $readerClass();
    }

    public function read(): string {
        $this->readFromDb();
        $this->setHeader();
        return $this->format();
    }
  }
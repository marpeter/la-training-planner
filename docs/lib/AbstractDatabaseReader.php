<?php
namespace TnFAT\Planner;

abstract class AbstractDatabaseReader {
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
    // override @deserialize in each concrete class to convert $this->data as returned from the
    // database into the desired format
    abstract protected function deserialize(): string;
    
    // @getTableNames is added to each concrete class by using the appropriate trait below
    abstract protected function getTableNames(): array;    

    public function echo() {
        $this->readFromDb();
        $this->setHeader();
        echo $this->deserialize();
    }
  }
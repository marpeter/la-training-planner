<?php
namespace LaPlanner;

function connectDB() {
    try {
        $connection = new \mysqli(
            getenv('LA_PLANNER_HOSTNAME'),
            getenv('LA_PLANNER_USERNAME'),
            getenv('LA_PLANNER_PASSWORD'),
            getenv('LA_PLANNER_DBNAME')
        );
        if ($connection->connect_error) {
            error_log('Cannot connect to DB: ' . mysqli_connect_error(), 0);
            return false;
        }
        return $connection;
    } catch( mysqli_sql_exception $ex ){
        error_log('Cannot connect to DB: ' . $ex->getMessage());
        return false;
    }
}

function connectDB_PDO() {
    try {
        $connection = new \PDO(
            'mysql:host=' . getenv('LA_PLANNER_HOSTNAME') . ';dbname=' . getenv('LA_PLANNER_DBNAME'),
            getenv('LA_PLANNER_USERNAME'),
            getenv('LA_PLANNER_PASSWORD'),
            array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                  \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                  \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ));
        return $connection;
    } catch( \PDOException $ex ){
        error_log('Cannot connect to DB: ' . $ex->getMessage());
        return false;
    }
}

function getDbVersion() {
    $dbConnection = connectDB_PDO();
    if($dbConnection) {
        $sql = 'SELECT field, field_val FROM version';
        foreach($dbConnection->query($sql) as $row) {
            $version[$row['field']] = $row['field_val'];
        }
        $version['withDB'] = true;
        return $version;
    } else {
        return [
            'number' => '0.14.200',
            'date' => '2025-05-13',
            'withDB' => false,
        ];
    }
}

abstract class AbstractTableReader {
    // override @tableNames in each concrete class to hold the array of table names to read from DB
    protected $tableNames = [];
    protected $data = null;

    protected function readFromDb() {
        $dbConnection = connectDB_PDO();
        foreach ($this->tableNames as $tableName) {
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
    protected function setHeader() {
        header('Content-Type: application/json');
    }
    // override @deserialize in each concrete class to convert $this->data as returned from the
    // database into the desired format
    abstract protected function deserialize();

    public function echo() {
        $this->readFromDb();
        $this->setHeader();
        echo $this->deserialize();
    }
  }

abstract class AbstractTableToCsvReader extends AbstractTableReader {
    // override @fileName in each concrete class to hold the desired download file name
    protected $fileName;
    protected function setHeader() {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $this->fileName . '"');
    }
    public function convertToCsv($data,$separator = ',') {
        $handle = fopen('php://temp', 'r+');
        foreach($data as $line) {
            fputcsv($handle, $line, $separator, '"');
        }
        rewind($handle);
        $contents = '';
        while (!feof($handle)) {
            $contents .= fread($handle, 8192);
        }
        fclose($handle);
        return $contents;
    }
}
?>
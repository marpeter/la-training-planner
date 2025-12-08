<?php
namespace LaPlanner;

function connectDB() {
    try {
        $env_file = __DIR__ . '/db.env';
        if (file_exists($env_file) && is_readable($env_file)) {
            $handle = fopen($env_file, 'r');
            while(($buffer = fgets($handle, 4096)) !== false) {
                $parts = explode('=',trim($buffer),2);
                if(count($parts) == 2) {
                    putenv(trim($parts[0]) . '=' . trim($parts[1]));
                }
            }
            fclose($handle);
        } 
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

function getDbVersion($keep_session=true) {
    $dbConnection = connectDB();
    if($dbConnection) {
        $sql = 'SELECT field, field_val FROM version';
        foreach($dbConnection->query($sql) as $row) {
            $version[$row['field']] = $row['field_val'];
        }
        $version['withDB'] = true;

        if( $keep_session) {
            session_start();
        } else {
            session_start(["read_and_close" => true]);
        }
        if( isset($_SESSION["username"]) ) {
            $version["username"] = $_SESSION["username"];
            // $version["userrole"] = "admin";
            $version["supportsEditing"] = true;
        } else {
            $version["supportsEditing"] = false;
        }

        return $version;
    } else {
        return [
            'number' => '0.14.200',
            'date' => '2025-05-13',
            'withDB' => false,
            'username' => false,
            'supportsEditing' => false,
        ];
    }
}

abstract class AbstractTableReader {
    protected $data = null;

    protected function readFromDb() {
        $dbConnection = connectDB();
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
    protected function setHeader() {
        header('Content-Type: application/json');
    }
    // override @deserialize in each concrete class to convert $this->data as returned from the
    // database into the desired format
    abstract protected function deserialize();
    
    // @getTableNames is added to each concrete class by using the appropriate trait below
    abstract protected function getTableNames();    

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

trait DisciplineTable {
    const HEADER_TABLE = 'disciplines';
    public function getTableNames() {
        return [self::HEADER_TABLE];
    }
}

trait ExerciseTable {
    const HEADER_TABLE = 'exercises';
    const LINK_DISCIPLINES_TABLE = 'exercises_disciplines';
    public function getTableNames() {
        return [self::HEADER_TABLE, self::LINK_DISCIPLINES_TABLE];
    }
}

trait FavoriteTable {
    const HEADER_TABLE = 'favorite_headers';
    const LINK_DISCIPLINES_TABLE = 'favorite_disciplines';
    const LINK_EXERCISES_TABLE = 'favorite_exercises';
    public function getTableNames() {
        return [self::HEADER_TABLE, self::LINK_DISCIPLINES_TABLE, self::LINK_EXERCISES_TABLE];
    }
}
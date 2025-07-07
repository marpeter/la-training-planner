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

  function getDbVersion() {
     define('FALLBACK_VERSION', [
      'number' => '0.14.200',
      'date' => '2025-05-13',
      'withDB' => false,
      'supportsEditing' => false,
      'supportsFavorites' => false,
    ]);
   $dbConnection = connectDB();
    if($dbConnection) {
      $sql = 'SELECT field, field_val FROM version';
      $result = $dbConnection->query($sql);
      if($result->num_rows > 0) {
          $data = $result->fetch_all(MYSQLI_ASSOC);
          $version = array();
          foreach($data as $row) {
              $version[$row['field']] = $row['field_val'];
          }
          $version['withDB'] = true;
          $version['supportsFavorites'] = (bool)$version['supportsFavorites'];
      } else {
          $version = FALLBACK_VERSION;
      }
      $dbConnection->close();
      return $version;
    } else {
      return FALLBACK_VERSION;
    }
  }

  abstract class AbstractTableReader {
    protected $tableNames = [];
    protected $data = null;

    protected function readFromDb() {
      $dbConnection = connectDB();
      foreach ($this->tableNames as $tableName) {
        $sql = "SELECT * FROM $tableName";
        $result = $dbConnection->query($sql);
        if ($result->num_rows > 0) {
          $this->data[$tableName] = $result->fetch_all(MYSQLI_ASSOC);
        } else {
          $this->data[$tableName] =  [];
        }
      }
      $dbConnection->close();
    }
    protected function setHeader() {
      header('Content-Type: application/json');
    }
    abstract protected function convert();

    public function echo() {
      $this->readFromDb();
      $this->setHeader();
      echo $this->convert();
    }
  }

  abstract class AbstractTableReaderCSV extends AbstractTableReader {
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
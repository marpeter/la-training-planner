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
          $version['supportsEditing'] = (bool)$version['supportsEditing'];
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

?>
<?php
  namespace LaPlanner;

  $error = "";
  include('db_connect.php');

  $dbConnection = connectDB();

  $sql = "SELECT * FROM FAVORITE_HEADERS";
  $result = $dbConnection->query($sql);
  if($result->num_rows > 0) {
      $favorites = $result->fetch_all(MYSQLI_ASSOC);
  } else {
      $favorites = [];
  }

  $sql = "SELECT * FROM FAVORITE_DISCIPLINES";
  $result = $dbConnection->query($sql);
  if($result->num_rows > 0) {
      $favoritesDisciplines = $result->fetch_all(MYSQLI_ASSOC);
  } else {
      $favoritesDisciplines = [];
  }
 
  $sql = "SELECT * FROM FAVORITE_EXERCISES";
  $result = $dbConnection->query($sql);
  if($result->num_rows > 0) {
      $exerciseMap = $result->fetch_all(MYSQLI_ASSOC);
  } else {
      $exerciseMap = [];
  }
  $dbConnection->close();
 
  if(isset($_GET['format']) && $_GET['format'] == 'csv') {
      // convert into CSV file as used by the non-php version of the app
      foreach ($favorites as &$favorite) {
          $favorite['Disciplines[]'] = array();
          foreach ($favoritesDisciplines as $favoriteDiscipline) {
              if ($favorite['id'] == $favoriteDiscipline['favorite_id']) {
                  $favorite['Disciplines[]'][] = $favoriteDiscipline['discipline_id'];
              }
          }
          $favorite['Disciplines[]'] = implode(":", $favorite['Disciplines[]']);       
      }

      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="Favorites.csv"');

      $csv_header = array_keys($favorites[0]);
      echo implode(",", $csv_header), "\n"; 
      $handle = fopen('php://temp', 'r+');
      $delimiter = ',';
      $enclosure = '"';
      foreach ($favorites as $line) {
          fputcsv($handle, $line, $delimiter, $enclosure);
      }
      rewind($handle);
      while (!feof($handle)) {
          $contents .= fread($handle, 8192);
      }
      fclose($handle);
      echo $contents, "\n";
      $contents = "";

      $csv_header = array_keys($exerciseMap[0]);
      echo implode(",", $csv_header), "\n";
      $handle = fopen('php://temp', 'r+');
      foreach($exerciseMap as $line) {
          fputcsv($handle, $line, $delimiter, $enclosure);
      }
      rewind($handle);
      while (!feof($handle)) {
          $contents .= fread($handle, 8192);
      }
      fclose($handle);
      echo $contents;

  } else {
      // convert into JSON format
      foreach ($favorites as &$favorite) {
          $favorite['disciplines'] = [];
          foreach ($favoritesDisciplines as $favoriteDiscipline) {
              if ($favorite['id'] == $favoriteDiscipline['favorite_id']) {
                  $favorite['disciplines'][] = $favoriteDiscipline['discipline_id'];
              }
          }
      }

      $contents = array('headers' => $favorites, 'exerciseMap' => $exerciseMap);
      header('Content-Type: application/json');
      echo json_encode($contents);
  }

?>
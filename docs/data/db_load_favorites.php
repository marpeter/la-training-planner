<?php
  namespace LaPlanner;
  
  function loadFavorites($data, &$messages) {

    // first check the header data of the favorites
    $headerFields = array('id', 'created_by','created_at','description','disciplines[]');
    $header = checkHeader( $data[0], $headerFields, 'Favoriten', $messages);
    if(!$header) {
      return 0;
    }

    $max=count($data);
    // parse the header data of the favorites - don't change the DB yet in case the "exercises map" is wrong
    $favorite_headers = [];
    for($line=1; $line<$max; $line++ ) {
      if(strlen(trim($data[$line]))==0) { // empty line separating header from exerciseMap reached
        $mapStart = $line+1;
        break;
      }
      $favorite_headers[] = fields_from_line($data[$line], $headerFields, $header);
    }
    // check the "exercises map" header
    $exerciseMapFields = array('favorite_id','phase','position','exercise_id','duration');
    $header = checkHeader($data[$mapStart], $exerciseMapFields, 'Favoriten', $messages);
        if(!$header) {
      return 0;
    }
    // parse the "exercise map"
    $favorite_exercises = [];
    for($line=++$mapStart; $line<$max; $line++ ) {
      $favorite_exercises[] = fields_from_line($data[$line], $exerciseMapFields, $header);
    }
    // process the data
    // 1. remove current database entries
    $dbConnection = connectDB();
    $dbConnection->autocommit(false);
    $rollback = false;

    $result = $dbConnection->query('DELETE FROM FAVORITE_EXERCISES');
    $result = $dbConnection->query('DELETE FROM FAVORITE_DISCIPLINES');
    $result = $dbConnection->query('DELETE FROM FAVORITE_HEADERS');
    // 2. insert the uploaded headers with the related disciplines
    $stmt_header = $dbConnection->prepare('INSERT INTO FAVORITE_HEADERS (id, created_by, created_at, description) VALUES (?, ?, ?, ?)');
    $stmt_header->bind_param('isss', $id, $created_by, $created_at, $descr);
    $stmt_dscplns = $dbConnection->prepare('INSERT INTO FAVORITE_DISCIPLINES (favorite_id, discipline_id) VALUES (?,?)');
    $stmt_dscplns->bind_param('is', $id, $discipline_id);
    foreach($favorite_headers as ['id' => $id, 'created_by' => $created_by, 'created_at' => $created_at,
                                  'description' => $descr, 'disciplines[]' => $disciplines]) {
      try {
        if(!$stmt_header->execute()) {
          $messages[] =  'Einfügen der Zeile $id, $created_by, $created_at, $descr ist fehlgeschlagen.';
          $rollback = true;
        } else {
          foreach($disciplines as $discipline_id) {
            if(!$stmt_dscplns->execute()) {
              $messages[] = 'Hinzufügen der Disziplin $discipline_id zu Favorit $id ist fehlgeschlagen.';
              $rollback = true;
            }
          }
        }
      } catch( mysqli_sql_exception $ex) {
        $error = $ex->getMessage();
        $messages[] = "Favorit $id in die Datenbank laden ist fehlgeschlagen, Fehler: $error.";
        $rollback = true;
      }
    }
    $stmt_dscplns->close();
    $stmt_header->close();
    // 3. insert the exercise-favorite relations
    $stmt_exmap = $dbConnection->prepare('INSERT INTO FAVORITE_EXERCISES (favorite_id, phase, position, exercise_id, duration) VALUES (?,?,?,?,?)');
    $stmt_exmap->bind_param('isisi',$favorite_id, $phase, $position, $exercise_id, $duration);
    foreach($favorite_exercises as ['favorite_id' => $favorite_id, 'phase' => $phase, 'position' => $position,
                                    'exercise_id' => $exercise_id, 'duration' => $duration]) {
      try {
        if(!$stmt_exmap->execute()) {
          $messages[] = 'Übung $exercise_id zu Favorit $favorite_id (Phase $phase, Pos. $position, Dauer $duration) ist fehlgeschlagen.';
          $rollback = true;
        }          
      } catch( mysqli_sql_exception $ex) {
        $error = $ex->getMessage();
        $messages[] = "Favorit $favorite_id mit Übung $exercise_id in die Datenbank laden ist fehlgeschlagen, Fehler: $error.";
        $rollback = true;
      }
    }
    $stmt_exmap->close();

    // 4. commit or rollback
    if($rollback) {
      $dbConnection->rollback();
      $dbConnection->close();
      $messages[] = "Es wurden keine Favoriten geladen.";
      return 0;
    } else {
      $dbConnection->commit();
      $dbConnection->close();
  
      $c1 = count($favorite_headers);
      $c2 = count($favorite_exercises);
      $messages[] = "Es wurden $c1 Favoriten mit insgesamt $c2 Übungen geladen.";
      return 1;
    }
  }
?>
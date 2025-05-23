<?php
  namespace LaPlanner;
  
  function loadExercises($data, &$messages) {

    $headerFields = array('id','name','warmup','runabc','mainex','ending','material','durationmin','durationmax','repeats','disciplines[]','details[]');
    $header = checkHeader($data[0], $headerFields, 'Übungs', $messages, ';');
    if(!$header) {
      return 0;
    }

    $exercises = [];
    $max=count($data);
    for($line=1; $line<$max; $line++) {
       $exercises[] = fields_from_line($data[$line], $headerFields, $header, ';');
    }

    // process the data
    // 1. remove current database entries, keeping "Auslaufen" because it's a special case
    $dbConnection = connectDB();
    $dbConnection->autocommit(false);
    $rollback = false;
    $result = $dbConnection->query('DELETE FROM EXERCISES WHERE NOT id="Auslaufen"');
    $result = $dbConnection->query('DELETE FROM EXERCISES_DISCIPLINES');
    // 2. insert the uploaded exercises with the related disciplines
    $stmt_header = $dbConnection->prepare('INSERT INTO EXERCISES (id, name, warmup, runabc, mainex, ending, material, durationmin, durationmax, repeats, details) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt_header->bind_param('ssiiiisiiss', $id, $name, $warmup, $runabc, $mainex, $ending, $material, $durationmin, $durationmax, $repeats, $details);
    $stmt_dscplns = $dbConnection->prepare('INSERT INTO EXERCISES_DISCIPLINES (exercise_id, discipline_id) VALUES (?,?)');
    $stmt_dscplns->bind_param('ss', $id, $discipline_id);
    foreach($exercises as ['id' => $id, 'name' => $name, 'warmup' => $warmup,  'runabc' => $runabc, 'mainex' => $mainex,
                           'ending' => $ending, 'material' => $material, 'durationmin' => $durationmin, 'durationmax' => $durationmax,
                           'repeats' => $repeats, 'details[]' => $detailsArray, 'disciplines[]' => $disciplines]) {
      $details = implode(':', $detailsArray);
      $warmup = $warmup=="true" ? 1 : 0;
      $runabc = $runabc=="true" ? 1 : 0;
      $mainex = $mainex=="true" ? 1 : 0;
      $ending = $ending=="true" ? 1 : 0;
      try {
        if(!$stmt_header->execute()) {
          $messages[] =  'Einfügen der Zeile $id, $name, ... ist fehlgeschlagen.';
          $rollback = true;
        } else {
          foreach($disciplines as $discipline_id) {
            if(!$stmt_dscplns->execute()) {
              $messages[] = 'Hinzufügen der Disziplin $discipline_id zu Übung $id ist fehlgeschlagen.';
              $rollback = true;
            }
          }
        }
      } catch( mysqli_sql_exception $ex) {
        $error = $ex->getMessage();
        $messages[] = "Übung $id in die Datenbank laden ist fehlgeschlagen, Fehler: $error.";
        $rollback = true;
      }
    }
    $stmt_dscplns->close();
    $stmt_header->close();

    // 3. commit or rollback
    if($rollback) {
      $dbConnection->rollback();
      $dbConnection->close();
      $messages[] = "Es wurden keine Übungen geladen.";
      return 0;
    } else {
      $dbConnection->commit();
      $dbConnection->close();
  
      $c1 = count($exercises);
      $messages[] = "Es wurden $c1 Übungen geladen.";
      return 1;
    }
  }
?>
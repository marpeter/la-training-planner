<?php
namespace LaPlanner;
include('../data/db_common.php');

function insertOrUpdate($exercise, $statement, $errorText) {
  $exercise['warmup'] = $exercise['warmup']=="true" ? 1 : 0;
  $exercise['runabc'] = $exercise['runabc']=="true" ? 1 : 0;
  $exercise['mainex'] = $exercise['mainex']=="true" ? 1 : 0;
  $exercise['ending'] = $exercise['ending']=="true" ? 1 : 0;
  try {
    $success = true;
    $messages = [];
    $dbConnection = connectDB();
    $dbConnection->autocommit(false);
    $stmt = $dbConnection->prepare($statement);
    $stmt->bind_param('siiiiiissss', $exercise['name'], $exercise['warmup'], $exercise['runabc'], $exercise['mainex'],
        $exercise['ending'], $exercise['durationmin'], $exercise['durationmax'], $exercise['material'], $exercise['repeats'],
        $exercise['details'], $exercise['id']);
    if(!$stmt->execute()) {
      $success = false;
      $messages[] = $errorText . $stmt->error;
      $stmt->close();
    } else {
      $stmt->close();
      $stmt = $dbConnection->prepare('DELETE FROM EXERCISES_DISCIPLINES WHERE exercise_id=?');
      $stmt->bind_param('s', $exercise['id']);
      $stmt->execute();
      $stmt->close();
      $stmt = $dbConnection->prepare('INSERT INTO EXERCISES_DISCIPLINES (exercise_id, discipline_id) VALUES (?, ?)');
      $stmt->bind_param('ss', $exercise['id'], $discipline_id);
      foreach($exercise['disciplines'] as $discipline_id){
        if(!$stmt->execute()) {
          $success = false;
          $messages[] = $errorText . $stmt->error;
        }
      }
    }
    if($success) {
      $dbConnection->commit();
      echo json_encode([
        'success' => true,
        'message' => $exercise]);
    } else {
      $dbConnection->rollback();
      echo json_encode([
        'success' => false,
        'message' => $messages]);
    }
  } catch( mysqli_sql_exception $ex) {
    $error = $ex->getMessage();
    $message = $errorText . $error;
    echo json_encode([
      'success' => false,
      'message' => $message,
    ]);
  } finally {
      $dbConnection->close();
  }
}

if(isset($_POST['update'])) {
    insertOrUpdate(
      json_decode($_POST['update'],true),
      'UPDATE EXERCISES SET name=?, warmup=?, runabc=?, mainex=?, ending=?, durationmin=?, durationmax=?, material=?, repeats=?, details=? WHERE id = ?',
      'Fehler beim Ändern der Übung: '
    );
} elseif(isset($_POST['create'])) {
    insertOrUpdate(
      json_decode($_POST['create'],true),
      'INSERT INTO EXERCISES (name, warmup, runabc, mainex, ending, durationmin, durationmax, material, repeats, details, id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
      'Fehler beim Erstellen der Übung: '
    );
} elseif(isset($_POST['delete'])) {
    $exerciseId = json_decode($_POST['delete'],true);
    try {
        $dbConnection = connectDB();
        $dbConnection->autocommit(false);
        $stmt = $dbConnection->prepare('DELETE FROM EXERCISES WHERE id = ?');
        $stmt->bind_param('s', $exerciseId);
        if(!$stmt->execute()) {
            throw new mysqli_sql_exception('Fehler beim Löschen der Übung: ' . $stmt->error);
        }
        $stmt->close();
        $stmt = $dbConnection->prepare('DELETE FROM EXERCISES_DISCIPLINES WHERE exercise_id=?');
        $stmt->bind_param('s', $exerciseId);
        if(!$stmt->execute()) {
            throw new mysqli_sql_exception('Fehler beim Löschen der Übung: ' . $stmt->error);
        }
        $stmt->close();
        $stmt = $dbConnection->prepare('DELETE FROM FAVORITE_EXERCISES WHERE exercise_id=?');
        $stmt->bind_param('s', $exerciseId);
        if(!$stmt->execute()) {
            throw new mysqli_sql_exception('Fehler beim Löschen der Übung: ' . $stmt->error);
        }
        $stmt->close();       
        $dbConnection->commit();
        echo json_encode([
            'success' => true,
            'message' => 'Übung erfolgreich gelöscht',
        ]);
    } catch (mysqli_sql_exception $ex) {
        $dbConnection->rollback();
        echo json_encode([
            'success' => false,
            'message' => 'Fehler beim Löschen der Übung: ' . $ex->getMessage(),
        ]);
    } finally {
        $dbConnection->close();
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No data received',
    ]);
}

?>
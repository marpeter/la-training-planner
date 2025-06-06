<?php
namespace LaPlanner;
include('../data/db_common.php');

if(isset($_POST['update'])) {

    $exercise = json_decode($_POST['update'],true);
    $exercise['warmup'] = $exercise['warmup']=="true" ? 1 : 0;
    $exercise['runabc'] = $exercise['runabc']=="true" ? 1 : 0;
    $exercise['mainex'] = $exercise['mainex']=="true" ? 1 : 0;
    $exercise['ending'] = $exercise['ending']=="true" ? 1 : 0;
    try {
      $success = true;
      $messages = [];
      $dbConnection = connectDB();
      $dbConnection->autocommit(false);
      $stmt = $dbConnection->prepare(
        'UPDATE EXERCISES SET name=?, warmup=?, runabc=?, mainex=?, ending=?, durationmin=?, durationmax=?, material=?, repeats=?, details=? WHERE id = ?');
      $stmt->bind_param('siiiiiissss', $exercise['name'], $exercise['warmup'], $exercise['runabc'], $exercise['mainex'],
        $exercise['ending'], $exercise['durationmin'], $exercise['durationmax'], $exercise['material'], $exercise['repeats'],
        $exercise['details'], $exercise['id']);
        if(!$stmt->execute()) {
          $success = false;
          $messages[] = 'Fehler beim ändern der Übung: ' . $stmt->error;
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
              $messages[] = 'Fehler beim ändern der Übung: ' . $stmt->error;
            }
          }
        };
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
        $dbConnection->close();
    } catch( mysqli_sql_exception $ex) {
      $error = $ex->getMessage();
      $message = "Fehler beim ändern der Übung:" . $error;
      echo json_encode([
        'success' => false,
        'message' => $message,
      ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No data received',
    ]);
}

?>
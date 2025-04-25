<?php
    $error = "";
    include('db_connect.php');

    $dbConnection = connectDB();

    $sql = "SELECT * FROM EXERCISES";
    $result = $dbConnection->query($sql);
    if($result->num_rows > 0) {
        $exercises = $result->fetch_all(MYSQLI_ASSOC);
        $sql = "SELECT * FROM EXERCISES_DISCIPLINES";
        $result = $dbConnection->query($sql);
        $exerciseDisciplines = $result->fetch_all(MYSQLI_ASSOC);
        foreach ($exercises as &$exercise) {
            $exercise['warmup'] = (bool)$exercise['warmup'];
            $exercise['runabc'] = (bool)$exercise['runabc'];
            $exercise['mainex'] = (bool)$exercise['mainex'];
            $exercise['ending'] = (bool)$exercise['ending'];
            $exercise['durationmin'] = (int)$exercise['durationmin'];
            $exercise['durationmax'] = (int)$exercise['durationmax'];
            $exercise['details'] = explode(":",$exercise['details']);
            $exercise['disciplines'] = [];
            foreach ($exerciseDisciplines as $exerciseDiscipline) {
                if ($exercise['id'] == $exerciseDiscipline['exercise_id']) {
                    $exercise['disciplines'][] = $exerciseDiscipline['discipline_id'];
                }
            }
        }
    } else {
        $exercises = [];
    }
    $dbConnection->close();

    header('Content-Type: application/json');
    echo json_encode($exercises);
?>
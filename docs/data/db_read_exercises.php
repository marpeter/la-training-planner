<?php
    $error = "";
    include('db_connect.php');

    $dbConnection = connectDB();

    $sql = "SELECT * FROM EXERCISES";
    $result = $dbConnection->query($sql);
    if($result->num_rows > 0) {
        $exercises = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $exercises = [];
    }

    $sql = "SELECT * FROM EXERCISES_DISCIPLINES";
    $result = $dbConnection->query($sql);
    if($result->num_rows > 0) {
        $exerciseDisciplines = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $exerciseDisciplines = [];
    }
    $dbConnection->close();
 
    if(isset($_GET['format']) && $_GET['format'] == 'csv') {
        // convert into CSV file as used by the non-php version of the app

        foreach ($exercises as &$exercise) {
            $exercise['warmup'] = $exercise['warmup']==1 ? "true" : "false";
            $exercise['runabc'] = $exercise['runabc']==1 ? "true" : "false";
            $exercise['mainex'] = $exercise['mainex']==1 ? "true" : "false";
            $exercise['ending'] = $exercise['ending']==1 ? "true" : "false";
            $exercise['durationmin'] = (int)$exercise['durationmin'];
            $exercise['durationmax'] = (int)$exercise['durationmax'];
            $exercise['Disciplines[]'] = array();
            foreach ($exerciseDisciplines as $exerciseDiscipline) {
                if ($exercise['id'] == $exerciseDiscipline['exercise_id']) {
                    $exercise['Disciplines[]'][] = $exerciseDiscipline['discipline_id'];
                }
            }
            $exercise['Disciplines[]'] = implode(":", $exercise['Disciplines[]']);
            unset($exercise['created_at']);         
        }

        $csv_header = array_keys($exercises[0]);
        $details_index = array_search("details", $csv_header);
        $csv_header[$details_index] = "Details[]";

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Exercises.csv"');

        echo implode(";", $csv_header), "\n"; 
        $handle = fopen('php://temp', 'r+');
        $delimiter = ';';
        $enclosure = '"';
        foreach ($exercises as $line) {
            // skip the "Auslaufen" exercise in the CSV to prevent it from being modified by mistake
            if ($line['id']=='Auslaufen') {
                continue;
            }
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

        header('Content-Type: application/json');
        echo json_encode($exercises);
    }

?>
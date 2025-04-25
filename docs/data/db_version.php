<?php
    $error = "";

    include('db_connect.php');
   
    $dbConnection = connectDB();
    $sql = 'SELECT field, field_val FROM version';
    $result = $dbConnection->query($sql);
    if($result->num_rows > 0) {
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $version = array();
        foreach($data as $row) {
            $version[$row['field']] = $row['field_val'];
        }
    } else {
        $version = 'Could not connect to DB';
    }
    $dbConnection->close();
    
    echo json_encode($version);
?>
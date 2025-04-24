<?php
    $error = "";

    include('db_connect.php');
   
    $dbConnection = connectDB();
    $sql = 'SELECT field_val as "number" FROM version WHERE field = "version"';
    $result = $dbConnection->query($sql);
    if($result->num_rows > 0) {
        $version = $result->fetch_assoc();
    } else {
        $version = 'Could not connect to DB';
    }
    $dbConnection->close();
    
    echo json_encode($version);
?>
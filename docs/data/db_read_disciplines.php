<?php
    $error = "";
    include('db_connect.php');

    $dbConnection = connectDB();
    
    $sql = "SELECT id, 'name', 'image' FROM DISCIPLINES";
    $result = $dbConnection->query($sql);
    if($result->num_rows > 0) {
        $data = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $data = [];
    }
    $dbConnection->close();

    echo json_encode($data);
?>
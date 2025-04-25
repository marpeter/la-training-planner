<?php
    $error = "";
    include('db_connect.php');

    $dbConnection = connectDB();
    
    $sql = "SELECT id, name, image FROM DISCIPLINES";
    $result = $dbConnection->query($sql);
    if($result->num_rows > 0) {
        $data = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $data = [];
    }
    $dbConnection->close();

    if(isset($_GET['format']) && $_GET['format'] == 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Disciplines.csv"');
        echo "Id,Name,Image\n";
        $handle = fopen('php://temp', 'r+');
        $delimiter = ',';
        $enclosure = '"';
        foreach ($data as $line) {
            fputcsv($handle, $line, $delimiter, $enclosure);
        }
        rewind($handle);
        while (!feof($handle)) {
            $contents .= fread($handle, 8192);
        }
        fclose($handle);
        echo $contents;
    } else {        
        header('Content-Type: application/json');
        echo json_encode($data);
    }
?>
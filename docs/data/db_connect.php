<?php

    function connectDB() {
        $connection = new mysqli(
            getenv('LA_PLANNER_HOSTNAME'),
            getenv('LA_PLANNER_USERNAME'),
            getenv('LA_PLANNER_PASSWORD'),
            getenv('LA_PLANNER_DBNAME'));

        if ($connection->connect_error) {
            die('Cannot connect to DB: ' . mysqli_connect_error());
        }
        return $connection;
    }
?>
<?php
namespace LaPlanner;

include('db_common.php');
   
$version = getDbVersion();
echo json_encode($version);
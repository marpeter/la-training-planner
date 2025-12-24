<?php
namespace LaPlanner;

require 'db_common.php';
   
$version = getDbVersion();
echo json_encode($version);
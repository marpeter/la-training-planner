<?php
  namespace LaPlanner;

  include('db_connect.php');
   
  $version = getDbVersion();
  echo json_encode($version);

?>
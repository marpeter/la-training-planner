<?php
namespace LaPlanner;
include('../data/db_common.php');

$version = getDbVersion(true);

session_unset();
session_destroy();
setcookie(session_name(), 'weg damit', 0, '/');

$url = isset($_GET['url']) ? nl2br($_GET['url']) : '../';

header("Location: $url");
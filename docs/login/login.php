<?php
namespace LaPlanner;
include('../data/db_common.php');

$version = getDbVersion(true);
$loginMessages = [];
$createUserMessages = [];

if( isset($version['username']) ) {
    if( isset($_POST['create_username']) && isset($_POST['create_password']) && isset($_POST['create_role']) ) {
        $user = new UserRecord(strtolower($_POST['create_username']), $_POST['create_password']);
        $user->setRole($_POST['create_role']);
        $user->create();
        $createUserMessages = $user->getMessages();
    }
} elseif( isset($_POST['username']) && isset($_POST['password']) ) {
    $user = new UserRecord($_POST['username'], $_POST['password']);
    if ( $user->logIn() ) {
        $version['username'] = $user->getName();
        $version['userrole'] = $user->getRole();
        $_SESSION['username'] = $user->getName();
    }
    $loginMessages = $user->getMessages();
}

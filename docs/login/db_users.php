<?php
namespace LaPlanner;
require '../data/db_common.php';

$version = getDbVersion(true);
$messages = [];

$loggedIn = isset($version['username']);

if( $loggedIn ) {
    $loginMenuItemDisabled = '';
    $loginButtonHref = "logout.php?url=./";

    $canManageUsers = ( isset($version['userrole']) && ( 
        $version['userrole']==='superuser' ||  $version['userrole']==='admin') );

    $filter = isset($_GET['filterBy']) ? $_GET['filterBy'] : '';
    $selectedUser = isset($_GET['selected']) ? $_GET['selected'] : '';

    if( $canManageUsers && isset($_POST['username']) 
            && isset($_POST['password']) && isset($_POST['role']) ) {
        $user = new UserRecord(strtolower($_POST['username']), $_POST['password']);
        $user->setRole($_POST['role']);
        switch($_POST['action']) {
            case 'create':
                $user->create();
                break;
            case 'update':
                $user->update();
                break;
            case 'delete':
                $user->delete();
                break;
        }
        $messages = $user->getMessages();
    }
    $users = UserRecord::readAll();
    if( $filter!=='' ) {
        $users = array_filter($users, function($user) use ($filter) {
            return str_starts_with($user->getName(), $filter);
        });
    }
    if( $selectedUser!=='' ) {
        $user = new UserRecord($selectedUser, '');
        $user->readFromDB();
        $username = $user->getName();
        $role = $user->getRole();
    } else {
        $username = '';
        $role = '---';
    }

} else {
    $loginMenuItemDisabled = $version['withDB'] === true ? '' : 'disabled';
    $loginButtonHref = "./";
}
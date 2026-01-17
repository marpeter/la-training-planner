<?php
namespace LaPlanner;
require '../data/db_common.php';

use \TnFAT\Planner\User\UserRecord;

$version = getDbVersion(true);
$messages = [];

$loggedIn = isset($version['username']);

if( $loggedIn ) {
    $loginMenuItemDisabled = '';
    $loginButtonHref = "logout.php?url=./";

    $canManageUsers = ( isset($version['userrole']) && ( 
        $version['userrole']==='superuser' ||  $version['userrole']==='admin') );

    $filter = getQueryString('filterBy');
    $selectedUser = getQueryString('selected');
    $action = getPostedString('action');

    if( $canManageUsers && $action !== '') {
        $username = getPostedString('username');
        $password = getPostedString('password');
        $role = getPostedString('role');

        $user = new UserRecord(strtolower($username), $password);
        $user->setRole($role);
        switch($action) {
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
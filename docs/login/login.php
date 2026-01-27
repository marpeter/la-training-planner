<?php
namespace LaPlanner;

require '../data/db_common.php';

use TnFAT\Planner\User\UserRecord;

$version = getDbVersion(true);
$messages = [];

if( isset($_POST['action']) && is_string($_POST['action']) ) {
    $username = getPostedString('username');
    $password = getPostedString('password');
    switch($_POST['action']) {
        case 'login':
            $user = new UserRecord($username, $password);
            if( $user->logIn() ) {
                $version['username'] = $user->getName();
                $version['userrole'] = $user->getRole();
                $_SESSION['username'] = $user->getName();              
            }
            $messages = $user->getMessages();
            break;
        case 'changePassword':
            $user = new UserRecord($version['username'], $password);
            $newPassword = getPostedString('new_password');
            $newPasswordRepeat = getPostedString('new_password_repeat');
            if( $user->logIn() && $newPassword == $newPasswordRepeat &&
                $user->setPassword($newPassword) ) {
                $user->update();
            }
            $messages = $user->getMessages();
            break;
        default:
            $messages[] = 'Unbekannte Aktion angefordert';
    }
}

$loggedIn = isset($version['username']);

if( $loggedIn ) {
    $canLogin = 'disabled';
    $loginMenuItemDisabled = '';
    $loginButtonHref = "logout.php?url=./";
} else {
    $canLogin = $version['withDB'] === true ? '' : 'disabled';
    $loginMenuItemDisabled = $canLogin;
    $loginButtonHref = "#";
}
$canManageUsers = (isset($version['userrole']) && ( 
    $version['userrole']==='superuser' ||  $version['userrole']==='admin') );
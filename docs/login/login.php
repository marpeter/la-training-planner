<?php
namespace LaPlanner;

require '../data/db_common.php';
$version = getDbVersion(true);
$messages = [];

if( isset($_POST['action']) ) {
    switch($_POST['action']) {
        case 'login':
            $user = new UserRecord($_POST['username'], $_POST['password']);
            if( $user->logIn() ) {
                $version['username'] = $user->getName();
                $version['userrole'] = $user->getRole();
                $_SESSION['username'] = $user->getName();              
            }
            $messages = $user->getMessages();
            break;
        case 'changePassword':
            $user = new UserRecord($version['username'], $_POST['password']);
            if( $user->login() && $user->setPassword($_POST['new_password']) ) {
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
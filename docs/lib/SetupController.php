<?php
namespace TnFAT\Planner;

require_once __DIR__ . '/../lib/autoload.php';

use \TnFAT\Planner\Utils;
use \TnFAT\Planner\User\UserRecord;
use \TnFAT\Planner\Setup\DatabaseInstaller;
use \TnFAT\Planner\Setup\DataLoader;

$version = Utils::getDbVersion();
$suMessages = [];
$dbMessages = [];

if( isset($_POST['action']) && is_string($_POST['action']) ) {
    switch($_POST['action']) {
        case 'setusers':
            $superUserName = Utils::getPostedString('su_name');
            $superUserPassword = Utils::getPostedString('su_password');
            $superUser = new UserRecord($superUserName, $superUserPassword);
            $superUser->setRole('superuser');

            $dbUserName = Utils::getPostedString('db_name');
            $dbUserPassword = Utils::getPostedString('db_password');
            $dbUser = new UserRecord($dbUserName, $dbUserPassword);
            
            if( $superUser->canBeCreated() ) {
                try {
                    $dbInstaller = new DatabaseInstaller();
                    if( $dbInstaller->install($dbUser->getName(), $dbUserPassword) ) {
                        $user = Utils::getUserInfo(true); // to start session
                        // recreate $superUser to refresh session with new settings
                        $superUser = new UserRecord($superUserName, $superUserPassword);
                        $superUser->setRole('superuser');
                        $superUser->create();
                        $superUser->logIn();
                        $_SESSION['username'] = $superUser->getName();

                        $loader = new DataLoader(DataLoader::ALL);
                        if($loader->load() === count(DataLoader::ALL)) {
                            header('Location: ./');
                        } else {
                            $dbMessages = $loader->getMessages();
                        }
                    }   
                    $dbMessages = array_merge($dbMessages, $dbInstaller->getMessages());
                } catch(\PDOException $ex) {
                    $dbMessages[] = $ex->getMessage();
                }
            }
            $suMessages = $superUser->getMessages();
            $dbMessages = array_merge($dbUser->getMessages(), $dbMessages);
            break;
    
        default:
            break;
    }
}
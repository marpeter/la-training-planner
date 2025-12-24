<?php
namespace LaPlanner;

include('../data/db_common.php');
$version = getDbVersion();
$suMessages = [];
$dbMessages = [];

class DatabaseInstaller {
    private $messages = [];
    private string $dbUserName;
    private string $dbUserPassword;
    private \PDO $dbConnection;

    public function __construct(string $dbAdminName, string $dbAdminPassword) {
        $this->dbConnection = connectDBUsing('localhost', '', $dbAdminName, $dbAdminPassword);
    }
    public function start(): bool {
        $okay = $this->createDbUser() &&
                $this->switchToDbUser() &&
                $this->createDbTables() &&
                $this->createEnvFile();
        return $okay;
    }
    public function getMessages(): array {
        return $this->messages;
    }

    private function createDbUser(): bool {
        $this->dbUserName = 'la_planner';
        $this->dbUserPassword = $this->randomPassword();

        try {
            // TODO: check if the current user has the right to create users or
            //       is already a limited user who can only create tables in their own db
            $stmt = $this->dbConnection->prepare(
                "CREATE OR REPLACE USER la_planner@localhost IDENTIFIED BY :password PASSWORD EXPIRE NEVER");
            $stmt->bindParam('password', $this->dbUserPassword);
            $stmt->execute();
            // TODO: remove once user and pwd are stored securely
            $this->messages[] = "la_planner Passwort: $this->dbUserPassword";
            $this->dbConnection->exec(
                "CREATE DATABASE IF NOT EXISTS la_planner CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->dbConnection->exec(
                "GRANT ALL PRIVILEGES ON la_planner.* TO la_planner@localhost");
            return true;
        } catch( \PDOException $ex) {
            $this->messages[] = $ex->getMessage();
            return false;
        }
    }
    private function switchToDbUser(): bool {
        try {
            $this->dbConnection = connectDBUsing(
                'localhost',
                'la_planner',
                $this->dbUserName,
                $this->dbUserPassword);
            return true;
        } catch( \PDOException $ex) {
            $this->messages[] = $ex->getMessage();
            return false;
        }
    }
    private function createDbTables(): bool {
        try {
            $sqlDir = dir(__DIR__ . '/sql');
            $files = [];
            while( ($file = $sqlDir->read()) !== false ) {
                if( str_ends_with($file, '.sql') ) {
                    $files[] = $file;
                }
            }
            sort($files);
            foreach( $files as $file ) {
                $this->dbConnection->exec(file_get_contents(__DIR__ . '/sql/' . $file));
            }
            return true;
        } catch( \PDOException $ex) {
            $this->messages[] = $ex->getMessage();
            return false;
        }
    }
    private function createEnvFile(): bool {
        $envContent = "LA_PLANNER_HOSTNAME=localhost\n" .
                      "LA_PLANNER_DBNAME=la_planner\n" .
                      "LA_PLANNER_USERNAME={$this->dbUserName}\n" .
                      "LA_PLANNER_PASSWORD={$this->dbUserPassword}\n";
        try {
            file_put_contents(__DIR__ . '/../data/db.env', $envContent);
            return true;
        } catch( \Exception $ex) {
            $this->messages[] = $ex->getMessage();
            return false;
        }
    }     
    private function randomPassword( $length = 12 ): string {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
        $password = substr( str_shuffle( $chars ), 0, $length );
        return $password;
    }
}


if( isset($_POST['action']) ) {
    switch($_POST['action']) {
        case 'setusers':
            $superUser = new UserRecord($_POST['su_name'] ?? '', $_POST['su_password'] ?? '');
            $superUser->setRole('superuser');

            $dbUser = new UserRecord($_POST['db_name'] ?? '', $_POST['db_password'] ?? '');
            $dbUser->setRole('admin'); // only to make canBeCreated() work
            if( $superUser->canBeCreated() && $dbUser->canBeCreated() ) {
                try {
                    $dbInstaller = new DatabaseInstaller($dbUser->getName(), $_POST['db_password'] ?? '');
                    $dbInstaller->start();

                    $dbMessages = $dbInstaller->getMessages();
                    // recreate $superUser to refresh DB connection with new settings
                    $version = getDbVersion(true);
                    $superUser = new UserRecord($_POST['su_name'] ?? '', $_POST['su_password'] ?? '');
                    $superUser->setRole('superuser');
                    $superUser->create();
                    $superUser->logIn();
                    $_SESSION['username'] = $superUser->getName();  
                    header('Location: admin.php');

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
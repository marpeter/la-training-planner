<?php
namespace TnFAT\Planner;

require_once __DIR__ . '/../lib/autoload.php';

use \TnFAT\Planner\Utils;
use \TnFAT\Planner\User\UserRecord;

$version = Utils::getDbVersion();
$suMessages = [];
$dbMessages = [];

class DatabaseInstaller {
    private $messages = [];
    private string $dbUserName;
    private string $dbUserPassword;
    private \PDO|false $dbConnection;

    public function install(string $dbAdminName, string $dbAdminPassword): bool {
        global $CONFIG;
        $CONFIG = [
            'dbhost' => 'localhost',
            'dbname'   => '',
            'dbuser' => $dbAdminName,
            'dbpassword' => $dbAdminPassword
        ];
        $this->dbConnection = Utils::connectDB();
        if(!$this->dbConnection ) {
            $this->messages[] = "Verbindung zur Datenbank nicht möglich.";
            return false;
        }
        if( $this->canCreateDbAndUser() ) {
            $okay = $this->createDbUser() &&
                    $this->switchToDbAndUser() &&
                    $this->createDbTables();
        } else {
            $database = $this->getDbInWhichToCreateTables();
            if( $database === null ) {
                $this->messages[] = "Datenbank-Installation nicht möglich";
                return false;
            }
            // use the provided user credentials
            $this->dbUserName = $CONFIG['dbuser'];
            $this->dbUserPassword = $CONFIG['dbpassword'];
            // set the database name
            $CONFIG['dbname'] = $database;
            $this->switchToDbAndUser($database);
            $okay = $this->createDbTables();
        }

        if( $okay ) {
            $okay = $this->createConfigFile($CONFIG);
            if( !$okay ) {
                $this->messages[] = "Fehler beim Erstellen der Konfigurationsdatei.";
            }
        }
        return $okay;
    }
    public function getMessages(): array {
        return $this->messages;
    }

    private function canCreateDbAndUser(): bool {
        try {
            $stmt = $this->dbConnection->prepare(
                "SELECT PRIVILEGE_TYPE FROM INFORMATION_SCHEMA.USER_PRIVILEGES"
                . " WHERE PRIVILEGE_TYPE IN ('CREATE USER', 'CREATE') AND "
                .       "GRANTEE = CONCAT(\"'\", SUBSTRING_INDEX(USER(), '@', 1), "
                .            "\"'@'\", SUBSTRING_INDEX(USER(), '@', -1), \"'\")");
            $stmt->execute();
            $privileges = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            return in_array('CREATE USER', $privileges) && in_array('CREATE', $privileges);
        } catch( \PDOException $ex) {
            $this->messages[] = htmlspecialchars($ex->getMessage());
            return false;
        }
    }

    private function getDbInWhichToCreateTables(): ?string {
        try {
            $stmt = $this->dbConnection->prepare(
                "SHOW DATABASES WHERE `Database` != 'information_schema'");
            $stmt->execute();
            $databases = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            $stmt = $this->dbConnection->prepare("SHOW GRANTS");
            $stmt->execute();
            $grants = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            // check if there is exactly one database with a GRANT to CREATE [tables] in it
            $databases = array_filter($databases, function($db) use ($grants) {
                foreach( $grants as $grant ) {
                    if(preg_match("/GRANT .*, CREATE,.* ON `$db`.*/", $grant) ) {
                        return true;
                    }
                }
                return false;
            });
            switch( count($databases) ) {
                case 0:
                    $this->messages[] = "Dem angegebenen Benutzer fehlen Rechte zum Anlegen von Tabellen in einer Datenbank.";
                    return null;
                case 1:
                    return $databases[0];
                default:
                    $this->messages[] = "Es wurde mehr als eine Datenbank gefunden, in der Tabellen angelegt werden könnten.";
                    return null;
            }
        } catch( \PDOException $ex) {
            $this->messages[] = htmlspecialchars($ex->getMessage());
            return null;
        }
    }
    private function createDbUser(): bool {
        $this->dbUserName = 'tfat_planner';
        $this->dbUserPassword = $this->randomPassword();

        try {
            $stmt = $this->dbConnection->prepare(
                "CREATE OR REPLACE USER tfat_planner@localhost IDENTIFIED BY :password PASSWORD EXPIRE NEVER");
            $stmt->bindParam('password', $this->dbUserPassword);
            $stmt->execute();
            $this->dbConnection->exec(
                "CREATE DATABASE IF NOT EXISTS tfat_planner CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->dbConnection->exec(
                "GRANT ALL PRIVILEGES ON tfat_planner.* TO tfat_planner@localhost");
            return true;
        } catch( \PDOException $ex) {
            $this->messages[] = $ex->getMessage();
            return false;
        }
    }
    private function switchToDbAndUser($dbname='tfat_planner'): bool {
        try {
            global $CONFIG;
            $CONFIG = [
                'dbhost' => 'localhost',
                'dbname'   => $dbname,
                'dbuser' => $this->dbUserName,
                'dbpassword' => $this->dbUserPassword   
            ];
            $this->dbConnection = Utils::connectDB();
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
    private function createConfigFile($CONFIG): bool {
        $configContent =
            "<?php\n\n" .
            '$CONFIG = ' .
            var_export($CONFIG, true) . ";\n";
        try {
            // Create the config directory if it doesn't exist yet
            if( !is_dir(__DIR__ . '/../config') ) {
                mkdir(__DIR__ . '/../config', 0755, true);
            }
            return file_put_contents(__DIR__ . '/../config/config.php', $configContent);
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
                        $version = Utils::getDbVersion(true);
                        // recreate $superUser to refresh DB connection with new settings
                        $superUser = new UserRecord($superUserName, $superUserPassword);
                        $superUser->setRole('superuser');
                        $superUser->create();
                        $superUser->logIn();
                        $_SESSION['username'] = $superUser->getName();  
                        header('Location: admin.php');
                    }   
                    $dbMessages = $dbInstaller->getMessages();
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
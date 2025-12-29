<?php
namespace LaPlanner;

require '../data/db_common.php';

$version = getDbVersion();
$suMessages = [];
$dbMessages = [];

class DatabaseInstaller {
    private $messages = [];
    private string $dbUserName;
    private string $dbUserPassword;
    private \PDO $dbConnection;

    public function __construct(string $dbAdminName, string $dbAdminPassword) {
        global $CONFIG;
        $CONFIG = [
            'dbhost' => 'localhost',
            'dbname'   => '',
            'dbuser' => $dbAdminName,
            'dbpassword' => $dbAdminPassword
        ];
        $this->dbConnection = connectDB();
    }
    public function install(): bool {
        global $CONFIG;
        $okay = $this->createDbUser() &&
                $this->switchToDbUser() &&
                $this->createDbTables();
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
            global $CONFIG;
            $CONFIG = [
                'dbhost' => 'localhost',
                'dbname'   => 'la_planner',
                'dbuser' => $this->dbUserName,
                'dbpassword' => $this->dbUserPassword   
            ];
            $this->dbConnection = connectDB();
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
                    if( $dbInstaller->install() ) {
                        $version = getDbVersion(true);
                        // recreate $superUser to refresh DB connection with new settings
                        $superUser = new UserRecord($_POST['su_name'] ?? '', $_POST['su_password'] ?? '');
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
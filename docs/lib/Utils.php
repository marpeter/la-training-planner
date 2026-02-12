<?php
namespace TnFAT\Planner;

use TnFAT\Planner\User\UserRecord;
global $CONFIG;
if( !isset($CONFIG) && file_exists(__DIR__ . '/../config/config.php') ) {
    include __DIR__ . '/../config/config.php';
}

class Utils {

    const ALLOWED_TAGS = '<b><em><i><strong>';

    static function connectDB(): \PDO|false {
        global $CONFIG;
        if( isset($CONFIG) ) {
            try {
                return new \PDO(
                    'mysql:host=' . $CONFIG['dbhost'] . ';dbname=' . $CONFIG['dbname'],
                    $CONFIG['dbuser'],
                    $CONFIG['dbpassword'],
                    [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                        \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"]
                );           
            } catch( \PDOException $ex ){
                error_log('Cannot connect to DB: ' . $ex->getMessage());
                return false;
            }
        } else {
            return false;
        }
    }

    static function getSessionInfo(): array {
        $version = self::getDbVersion();
        $user = self::getUserInfo();
        return [$version, $user];       
    }

    static function getUserInfo($keep_session=false): array {
        if( $keep_session) {
            session_start();
        } else {
            session_start(["read_and_close" => true]);
        }
        $userInfo['canEdit'] = false;
        if( isset($_SESSION['username']) ) {
            $user = new UserRecord($_SESSION['username'], '');
            $user->readFromDB();
            $userInfo['name'] = $user->getName();
            $userInfo['role'] = $user->getRole();
            if( $userInfo['role']==='admin' || $userInfo['role']==='superuser' ) {
                $userInfo['canEdit'] = true;
            }
        }
        return $userInfo;
    }

    static function getDbVersion(): array {
        $dbConnection = self::connectDB();
        if($dbConnection) {
            $sql = 'SELECT field, field_val FROM version';
            foreach($dbConnection->query($sql) as $row) {
                $version[$row['field']] = $row['field_val'];
            }
            $version['withDB'] = true;
            return $version;
        } else {
            return [
                'number' => '0.14.200',
                'date' => '2025-05-13',
                'withDB' => false,
            ];
        }
    }

    static function getPostedString( string $fieldName ): string {
        if( isset($_POST[$fieldName]) && is_string($_POST[$fieldName]) ) {
            return trim($_POST[$fieldName]);
        } else {
            return '';
        }
    }
    
    static function getQueryString( string $fieldName ): string {
        if( isset($_GET[$fieldName]) && is_string($_GET[$fieldName]) ) {
            return trim($_GET[$fieldName]);
        } else {
            return '';
        }
    }

    static function readFromFile($realFileName,$logicalFileName, &$messages): array|false {
        $handle = fopen($realFileName, 'r');
        if($handle) {
            $content = [];
            while(($buffer = fgets($handle, 4096)) !== false) {
                $content[] = $buffer;
            }
            fclose($handle);
            return $content;
        } else {  
            $messages[] = "Die hochgeladene Datei $logicalFileName kann nicht geöffnet werden.";
            return false;
        }
    }    
}
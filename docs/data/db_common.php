<?php
namespace LaPlanner;

spl_autoload_register(function ($class) {

    // error_log("Autoloading class: $class");
    // project-specific namespace prefix
    // TnFAT = Track and Field Athletic Training
    $prefix = 'TnFAT\\Planner\\';

    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/../lib/';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // error_log("Looking for file: $file");
    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

if( file_exists(__DIR__ . '/../config/config.php') ) {
    include __DIR__ . '/../config/config.php';
}

const ALLOWED_TAGS = '<b><em><i><strong>';
function getPostedString( string $fieldName ): string {
    if( isset($_POST[$fieldName]) && is_string($_POST[$fieldName]) ) {
        return trim($_POST[$fieldName]);
    } else {
        return '';
    }
}
function getQueryString( string $fieldName ): string {
    if( isset($_GET[$fieldName]) && is_string($_GET[$fieldName]) ) {
        return trim($_GET[$fieldName]);
    } else {
        return '';
    }
}

function connectDB(): \PDO|false {
    global $CONFIG;
    if( isset($CONFIG) ) {
        try {
            $connection = new \PDO(
                'mysql:host=' . $CONFIG['dbhost'] . ';dbname=' . $CONFIG['dbname'],
                $CONFIG['dbuser'],
                $CONFIG['dbpassword'],
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"]
            );
            return $connection;            
        } catch( \PDOException $ex ){
            error_log('Cannot connect to DB: ' . $ex->getMessage());
            return false;
        }
    } else {
        return false;
    }
}

function getDbVersion($keep_session=true): array {
    global $CONFIG;
    $dbConnection = connectDB();
    if($dbConnection) {
        $sql = 'SELECT field, field_val FROM version';
        foreach($dbConnection->query($sql) as $row) {
            $version[$row['field']] = $row['field_val'];
        }
        $version['withDB'] = true;

        if( $keep_session) {
            session_start();
        } else {
            session_start(["read_and_close" => true]);
        }
        $version['supportsEditing'] = false;
        if( isset($_SESSION['username']) ) {
            $user = new \TnFAT\Planner\User\UserRecord($_SESSION['username'], '');
            $user->readFromDB();
            $version['username'] = $user->getName();
            $version['userrole'] = $user->getRole();
            if( $version['userrole']==='admin' || $version['userrole']==='superuser' ) {
                $version['supportsEditing'] = true;
            }
        }
        return $version;
    } else {
        return [
            'number' => '0.14.200',
            'date' => '2025-05-13',
            'withDB' => false,
            'supportsEditing' => false,
        ];
    }
}
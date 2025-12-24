<?php
namespace LaPlanner;

include '../config/config.php';

function connectDB() {
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

// Note that instances of the following class represent a single user
// record in the DB, not the whole table like the DataSaver instances.
class UserRecord {
    private string $name;
    private string $password;
    private string $passwordHash = '';
    private string $role = '';
    private $dbConnection = null;
    private array $messages = [];

    public static function readAll(): array {
        try {
            $dbConnection = connectDB();
            $result = $dbConnection->query('SELECT * FROM users');
            $users = [];
            if ($result) {
                $userRecords = $result->fetchAll();
                foreach($userRecords as $userRecord) {
                    $user = new static($userRecord['username'],'');
                    $user->role = $userRecord['role'];
                    $user->passwordHash = $userRecord['password'];
                    $users[] = $user;
                }
            } 
            $result = null;
            $dbConnection = null;
            return $users;
        } catch (\PDOException $ex) {
            $this->messages[] = $ex->getMessage();
            return [];
        } 
    }

    public function __construct(string $name, string $password) {
        $this->name = $name;
        $this->password = $password;
        $this->dbConnection = connectDB();
    }
    public function __destructor(): void {
        $this->dbConnection = null;
    }

    public function getRole(): string {
        return $this->role;
    }
    public function getName(): string {
        return $this->name;
    }
    public function getMessages(): array {
        return $this->messages;
    }
    // Set the role, not checking that it is valid
    public function setRole(string $role): void {
        $this->role = $role;
    }
    // Set the (new) password, checking that it is allowed
    public function setPassword(string $password): bool {
        $this->password = $password;
        return $this->hasAllowedPassword();
    }
    // Set a random password and return it
    public function setRandomPassword(int $length = 12): string {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
        do {
            $this->password = substr( str_shuffle( $chars ), 0, $length );
        } while( !($this->hasAllowedPassword() && ctype_alpha($this-password[0])) );
        return $this->password;
    }
    public function logIn(): bool {
        try {
            $this->readFromDB();
            if( password_verify($this->password, $this->passwordHash) ) {
                if( password_needs_rehash($this->passwordHash, PASSWORD_DEFAULT) ) {
                    return $this->update();
                }
                return true;
            } else {
                $this->messages[] = 'Da stimmte etwas mit Benutzername oder Passwort nicht ...';
                return false;
            }
        } catch (\PDOException $ex) {
            $this->messages[] = $ex->getMessage();
            return false;
        }       
    }

    public function readFromDB(): bool {
        try {
            $stmt = $this->dbConnection->prepare('SELECT password, role FROM users WHERE username = :username');
            $stmt->bindParam('username', $this->name, \PDO::PARAM_STR);
            $stmt->execute();
            $stmt->bindColumn('password', $this->passwordHash);
            $stmt->bindColumn('role', $this->role);
            $stmt->fetch(\PDO::FETCH_BOUND);
            return true;
        } catch (\PDOException $ex) {
            $this->messages[] = $ex->getMessage();
            return false;
        }  
    }

    public function create(): bool {
        if( $this->canBeCreated() ) {
            try {
                $password = password_hash($this->password, PASSWORD_DEFAULT);
                $stmt = $this->dbConnection->prepare('INSERT INTO users' . 
                    ' (username,  password,  role) VALUES ' .
                    '(:username, :password, :role)');
                $stmt->bindParam('username', $this->name, \PDO::PARAM_STR);
                $stmt->bindParam('password', $password, \PDO::PARAM_STR);
                $stmt->bindParam('role', $this->role, \PDO::PARAM_STR);
                $stmt->execute();
                $this->messages[] = "Benutzer $this->name wurde mit Rolle "
                    . "$this->role angelegt.";
                return true;
            } catch(\PDOException $ex) {
                $this->messages[] = $ex->getMessage();
                return false;
            }
        } else {
            return false;
        }
    }

    public function update(): bool {
        if( $this->canBeCreated() ) {
            try {
                $this->passwordHash = password_hash($this->password, PASSWORD_DEFAULT);
                $stmt = $this->dbConnection->prepare('UPDATE users SET ' . 
                    'password=:password, role=:role ' .
                    'WHERE username=:username' );
                $stmt->bindParam('username', $this->name, \PDO::PARAM_STR);
                $stmt->bindParam('password', $this->passwordHash, \PDO::PARAM_STR);
                $stmt->bindParam('role', $this->role, \PDO::PARAM_STR);
                $stmt->execute();
                $this->messages[] = "Benutzer $this->name wurde mit Rolle "
                    . "$this->role aktualisiert.";
                return true;                
            } catch(\PDOException $ex) {
                $$this->messages[] = $ex->getMessage();
                return false;
            }
        } else {
            return false;
        }
    }

    public function delete(): bool {
        try {
            $stmt = $this->dbConnection->prepare('DELETE FROM users WHERE' .
                ' username=:username');
            $stmt->bindParam('username', $this->name, \PDO::PARAM_STR);
            $stmt->execute();
            $this->messages[] = "Benutzer $this->name wurde gelöscht.";
            return true;
        } catch(\PDOException $ex) {
            $$this->messages[] = $ex->getMessage();
            return false;
        }
    }

    public function canBeCreated(): bool {
        return $this->hasAllowedUsername()
            && $this->hasAllowedPassword()
            && $this->hasValidRole();
    }

    // Check that the username only contains reasonable characters
    protected function hasAllowedUsername(): bool {
        if( preg_match('/^[a-zäöüßA-ZÄÖÜ0-9\.\-_@]{4,20}$/', $this->name) ) {
            return true;
        } else {
            $this->messages[] = "Benutzername $this->name enhält ungültige Zeichen, ist zu kurz oder zu lang.";
            $this->messages[] = 'Er muss aus 4-20 Buchstaben, Ziffern, ., _, @ und - bestehen.';
            return false;
        }
    }  

    // Check that the password has a minimum length
    // TODO: also check for minimum complexity?
    protected function hasAllowedPassword(): bool {
        if( !preg_match('/^\s/', $this->password) && !preg_match('/\s$/', $this->password) 
                && strlen($this->password) > 4 ) {
            return true;
        } else {
            $this->messages[] = 'Das Passwort muss mindestens 5 Zeichen lang sein ';
            $this->messages[] = 'und darf nicht mit Leerzeichen beginnen oder enden.';
            return false;
        }
    }

    protected function hasValidRole(): bool {
        if( in_array($this->role, ['user', 'admin', 'superuser']) ) {
            return true;
        } else {
            $this->messages[] = 'Es gibt keine Rolle ' . $this->role;
            return false;
        }        
    }
}

function getDbVersion($keep_session=true) {
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
            $user = new UserRecord($_SESSION['username'], '');
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

abstract class AbstractTableReader {
    protected $data = null;

    protected function readFromDb() {
        $dbConnection = connectDB();
        foreach ($this->getTableNames() as $tableName) {
            $sql = "SELECT * FROM $tableName";
            $result = $dbConnection->query($sql);
            if ($result) {
                $this->data[$tableName] = $result->fetchAll();
            } else {
                $this->data[$tableName] = [];
            } 
        }
        $result = null;
        $dbConnection = null;
    }
    protected function setHeader() {
        header('Content-Type: application/json');
    }
    // override @deserialize in each concrete class to convert $this->data as returned from the
    // database into the desired format
    abstract protected function deserialize();
    
    // @getTableNames is added to each concrete class by using the appropriate trait below
    abstract protected function getTableNames();    

    public function echo() {
        $this->readFromDb();
        $this->setHeader();
        echo $this->deserialize();
    }
  }

abstract class AbstractTableToCsvReader extends AbstractTableReader {
    // override @fileName in each concrete class to hold the desired download file name
    protected $fileName;
    protected function setHeader() {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $this->fileName . '"');
    }
    public function convertToCsv($data,$separator = ',') {
        $handle = fopen('php://temp', 'r+');
        foreach($data as $line) {
            fputcsv($handle, $line, $separator, '"');
        }
        rewind($handle);
        $contents = '';
        while (!feof($handle)) {
            $contents .= fread($handle, 8192);
        }
        fclose($handle);
        return $contents;
    }
}

trait DisciplineTable {
    const HEADER_TABLE = 'disciplines';
    public function getTableNames() {
        return [self::HEADER_TABLE];
    }
}

trait ExerciseTable {
    const HEADER_TABLE = 'exercises';
    const LINK_DISCIPLINES_TABLE = 'exercises_disciplines';
    public function getTableNames() {
        return [self::HEADER_TABLE, self::LINK_DISCIPLINES_TABLE];
    }
}

trait FavoriteTable {
    const HEADER_TABLE = 'favorite_headers';
    const LINK_DISCIPLINES_TABLE = 'favorite_disciplines';
    const LINK_EXERCISES_TABLE = 'favorite_exercises';
    public function getTableNames() {
        return [self::HEADER_TABLE, self::LINK_DISCIPLINES_TABLE, self::LINK_EXERCISES_TABLE];
    }
}
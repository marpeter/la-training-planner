<?php
namespace TnFAT\Planner\User;

// Note that instances of the following class represent a single user
// record, not the whole table like the DatabaseTable instances.
class UserRecord {
    private ?int $id;
    private string $name;
    private string $password;
    private string $passwordHash = '';
    private string $role = '';
    private array $messages = [];
    private DatabaseTable $dbTable;

    public static function readAll(): array {
        $dbTable = new DatabaseTable();
        $userRecords = [];
        $users = $dbTable->read()[DatabaseTable::HEADER_TABLE];
        foreach($users as $user) {
            $userRecord = new static($user['username'], '');
            $userRecord->id = $user['id'];
            $userRecord->passwordHash = $user['password'];
            $userRecord->role = $user['role'];
            $userRecords[] = $userRecord;
        }
        return $userRecords;
    }

    public function __construct(string $name, string $password) {
        $this->id = 0;
        $this->name = $name;
        $this->password = $password;
        $this->dbTable = new DatabaseTable();
    }

    public function getId(): string {
        return $this->id;
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
        $user = $this->dbTable->read($this->name);
        if( is_array($user) && count($user)==1) {
            $this->id = $user[0]['id'];
            $this->name = $user[0]['username'];
            $this->role = $user[0]['role'];
            $this->passwordHash = $user[0]['password'];
            return true;
        } else {
            $this->messages[] = "No user with name $this->name found";
            return false;
        } 
    }

    public function create(): bool {
        if( $this->hasAllowedPassword() ) {
            try {
                $password = password_hash($this->password, PASSWORD_DEFAULT);
                $result = $this->dbTable->create([
                    'username' => $this->name,
                    'password' => $password,
                    'role' => $this->role,
                    ]);
                if( $result['success'] ) {
                    $this->readFromDB(); // to get the id
                    $this->messages[] = "Benutzer $this->name wurde mit Rolle "
                        . "$this->role angelegt.";
                    return true;
                } else {
                    $this->messages[] = $result['message'];
                    return false;
                };
            } catch(\PDOException $ex) {
                $this->messages[] = $ex->getMessage();
                return false;
            }
        } else {
            return false;
        }
    }

    public function update(): bool {
        if( $this->password !== '') {
            $this->passwordHash = password_hash($this->password, PASSWORD_DEFAULT);
        }
        if( ( $this->password === '' && $this->passwordHash !== '' ) // assume password left unchanged 
            || $this->hasAllowedPassword() ) {
            try {
                $result = $this->dbTable->update([
                    'id' => $this->id,
                    'username' => $this->name,
                    'password' => $this->passwordHash,
                    'role' => $this->role,
                    ]);
                if( $result['success'] ) {
                    $this->messages[] = "Benutzer $this->name wurde mit Rolle "
                        . "$this->role aktualisiert.";
                    return true;
                } else {
                    $this->messages[] = $result['message'];
                    return false;
                };                
            } catch(\PDOException $ex) {
                $this->messages[] = $ex->getMessage();
                return false;
            }
        } else {
            return false;
        }
    }

    public function delete(): bool {
        try {
            $this->dbTable->delete($this->id);
            $this->messages[] = "Benutzer $this->name wurde gelöscht.";
            return true;
        } catch(\PDOException $ex) {
            $$this->messages[] = $ex->getMessage();
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
}
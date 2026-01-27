<?php
namespace TnFAT\Planner\User;

class DatabaseTable extends \TnFAT\Planner\AbstractDatabaseTable {

    const HEADER_TABLE = 'users';
    protected $ENTITY_LOCALIZED = "Benutzer";
    protected string $entity = "user";

    public function read(?string $id=null): array {
        if( $id===null ) {
            return parent::read();
        } else {
            $dbConnection = \LaPlanner\connectDB();
            $stmt = $dbConnection->prepare('SELECT id, password, role FROM users WHERE username = :username');
            $stmt->bindParam('username', $id, \PDO::PARAM_STR);
            $stmt->execute();
            $stmt->bindColumn('id', $rowId);
            $stmt->bindColumn('password', $passwordHash);
            $stmt->bindColumn('role', $role);
            $stmt->fetch(\PDO::FETCH_BOUND);
            return [['id' => $rowId, 'username' => $id, 'password' => $passwordHash, 'role' => $role]];
        }
    }

    public function getTableNames(): array {
        return [self::HEADER_TABLE];
    }

    public function createEntityBulk(array $exercises, ?\PDO $dbConnection): array {
        return ['Not supported yet'];
    }

    protected function createEntity(array $user): void {
        try { 
            $stmt = $this->dbConnection->prepare(
                'INSERT INTO ' . self::HEADER_TABLE . 
                ' (username,  password,  role) VALUES ' .
                '(:username, :password, :role)');
            $stmt->bindParam('username', $user['username'], \PDO::PARAM_STR);
            $stmt->bindParam('password', $user['password'], \PDO::PARAM_STR);
            $stmt->bindParam('role', $user['role'], \PDO::PARAM_STR);
            $stmt->execute();
            $stmt = null;
        } catch (\PDOException $ex) {
            throw new \PDOException('Fehler beim Erstellen des Benutzers ' . 
                $user['username'] . ' : ' . $ex->getMessage());
        }
    }

    protected function updateEntity(array $user): void {
        try {
            $stmt = $this->dbConnection->prepare(
                'UPDATE ' . self::HEADER_TABLE 
                . ' SET password=:password, role=:role ' // TODO: allow changing the name?
                .'WHERE id=:id' );
            $stmt->bindParam('id', $user['id'], \PDO::PARAM_INT);
            // $stmt->bindParam('username', $user['name'], \PDO::PARAM_STR);
            $stmt->bindParam('password', $user['password'], \PDO::PARAM_STR);
            $stmt->bindParam('role', $user['role'], \PDO::PARAM_STR);
            $stmt->execute();
            $stmt = null;
        } catch (\PDOException $ex) {
            throw new \PDOException('Fehler beim Ändern des Benutzers: ' . $ex->getMessage());
        }
    }

    protected function deleteEntity(string $userId): void {
        try {
            $stmt = $this->dbConnection->prepare('DELETE FROM ' .
                 self::HEADER_TABLE . ' WHERE id = :id');
            $stmt->bindParam('id', $userId, \PDO::PARAM_STR);
            $stmt->execute();
        } catch (\PDOException $ex) {
            throw new \PDOException('Fehler beim Löschen des Benutzers: ' . $ex->getMessage());
        }
    }

    protected function sanitizeAndValidateEntity(array &$user): void {
        if( !is_string($user['password']) || $user['password']=='' ) {
            throw new \PDOException('Das Passwort muss ein nicht-leerer String sein.');
        }
        if( !(is_string($user['role']) && in_array($user['role'], ['user', 'admin', 'superuser'])) ) {
            throw new \PDOException('Es muss eine gültige Rolle angegeben werden.');
        }
        // Check that the username only contains reasonable characters
        if( !(is_string($user['username']) && preg_match('/^[a-zäöüßA-ZÄÖÜ0-9\.\-_@]{4,100}$/', $user['username'])) ) {
            throw new \PDOException('Der angegebene Benutzername enhält ungültige Zeichen, ist zu kurz oder zu lang.' 
                . 'Er muss aus 4-100 Buchstaben, Ziffern, ., _, @ und - bestehen.');
        }
    }

}
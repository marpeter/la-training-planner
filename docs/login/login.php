<?php
namespace LaPlanner;
include('../data/db_common.php');

function isLoginValid(string $username, string $password, array &$version, array &$messages): bool  {
    try {
        $dbConnection = connectDB();
        $stmt = $dbConnection->prepare('SELECT password, role FROM users WHERE username = :username');
        $stmt->bindParam('username', $username, \PDO::PARAM_STR);
        $stmt->execute();
        $stmt->bindColumn('password', $passwordInDB);
        $stmt->bindColumn('role', $role);
        $stmt->fetch(\PDO::FETCH_BOUND);

        if( password_verify($password, $passwordInDB) ) {
            if( password_needs_rehash($passwordInDB, PASSWORD_DEFAULT) ) {
                // TODO: save rehashed password in DB
            }
            $version['username'] = $username;
            $version['userrole'] = $role;
            return true;
        } else {
            return false;
        }
    } catch (\PDOException $ex) {
        $messages[] = $ex->getMessage();
        return false;
    }
}

$version = getDbVersion(true);
$loginMessages = [];
$createUserMessages = [];

if( isset($version['username']) ) {
    if( isset($_POST['create_username']) && isset($_POST['create_password']) && isset($_POST['create_role']) ) {
        // TODO: check that create_username only contains reasonable characters
        // TODO: check that create_password has a minimum length (and complexity?)
        // TODO: check that the role is valid
        $password = password_hash($_POST['create_password'], PASSWORD_DEFAULT);
        try {
            $dbConnection = connectDB();
            $stmt = $dbConnection->prepare('INSERT INTO users' . 
                ' (username,  password,  role) VALUES ' .
                '(:username, :password, :role)');
            $stmt->bindParam('username', $_POST['create_username'], \PDO::PARAM_STR);
            $stmt->bindParam('password', $password, \PDO::PARAM_STR);
            $stmt->bindParam('role', $_POST['create_role'], \PDO::PARAM_STR);
            $stmt->execute();
            $createUserMessages[] = 'Benutzer ' . $_POST['create_username'] . 
                ' wurde mit Rolle ' . $_POST['create_role'] . ' angelegt.';
        } catch(\PDOException $ex) {
            $createUserMessages[] = $ex->getMessage();
        }
    }
} elseif( isset($_POST['username']) && isset($_POST['password']) ) {
    if ( isLoginValid($_POST['username'], $_POST['password'], $version, $loginMessages) ) {
        $_SESSION['username'] = $version['username'];
    } else {
        $loginMessages[] = 'Da stimmte etwas mit Benutzername oder Passwort nicht ...';
    }
}

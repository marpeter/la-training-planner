<?php
namespace TnFAT\Planner;

require_once 'data/db_common.php';

use \TnFAT\Planner\User\UserRecord;

class UserController {

    public static function handle(array $pathTokens): void {
        if (count($pathTokens) < 1) {
            echo json_encode([
                'success' => false,
                'message' => 'Missing user id or action in request URI.',
            ]);
            exit;
        }

        $action = filter_var(array_shift($pathTokens), FILTER_SANITIZE_STRING, FILTER_NULL_ON_FAILURE);

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                if( is_numeric($action) ) {
                    echo json_encode(self::getSingleUser((int)$action));
                } else {
                    switch($action) {
                        case 'list':
                            $users = self::getUserList($pathTokens);
                            echo json_encode($users);
                            break;
                        case 'logout':
                            // echo json_encode(self::logout());
                            self::logout();
                            break;
                        default:
                            http_response_code(405);
                            echo "Unsupported action: " . htmlspecialchars($action);
                    }
                }
                break;

            case 'POST':
                if( is_numeric($action) ) {
                    echo json_encode(self::updateUser((int)$action));
                } else {
                    switch($action) {
                        case 'login':
                            echo json_encode(self::login());
                            break;

                        default:
                            http_response_code(405);
                            echo "Unsupported action: " . htmlspecialchars($action);
                    }  
                }
                break;

            default:
                http_response_code(405);
                echo "Unsupported HTTP method: " . htmlspecialchars($_SERVER['REQUEST_METHOD']);
                exit;
        }
    }

    private static function getSingleUser(int $userId): array {
        $version = \LaPlanner\getDbVersion(true);
        // TODO: add support for direct read by Id
        $userRecords = UserRecord::readAll();

        // if the current user has no "manage user" privilege, only the user itself
        // should be findable
        if( !UserController::currentUserCanManageUsers() ) {
            $userName = $version['username'];
            $userRecords = array_filter($userRecords, function($user) use ($userName) {
                return $user->getName() === $userName; });
        }
        $user = array_map(
            '\TnFAT\Planner\UserController::userToArray',
            array_filter($userRecords, function($user) use ($userId) {
                return str_starts_with($user->getId(), $userId);
            })
        );

        if( count($user) == 1 ) {
            return $user[array_key_first($user)];
        } else {
            return [ 'success' => false, 'message' => "Kein Benutzer mit Id $userId vorhanden." ];
        }
    }

    private static function getUserList($params): array {
        if( self::currentUserCanManageUsers() ) {
            $userRecords = UserRecord::readAll();
            $filter = \LaPlanner\getQueryString('filterBy');

            return array_map(
                '\TnFAT\Planner\UserController::userToArray',
                array_filter($userRecords, function($user) use ($filter) {
                    return str_starts_with($user->getName(), $filter);
                })
            );
        } else {
            return [ 'success' => false, 'message' => "Nur Administratoren dürfen die Benutzerliste lesen" ];
        }
    }

    private static function updateUser(int $userId): array {
        $action = \LaPlanner\getPostedString("verb");

        if( self::currentUserCanManageUsers() ) {
            $data = json_decode(\LaPlanner\getPostedString("data"), true);
            $username = array_key_exists('name', $data) ? $data['name']: '';
            $password = array_key_exists('password', $data) ? $data['password'] : '';
            $role = array_key_exists('role', $data) ? $data['role'] : '';

            switch($action) {
                case 'create':
                    $user = new UserRecord(strtolower($username), $password);
                    $user->setRole($role);
                    if( $user->create() ) {
                        $user->readFromDb();
                        return [ 'success' => true,
                                 'data' => UserController::userToArray($user) ];
                    } else {
                        return [ 'success' => false,
                                 'message' => $user->getMessages()[0] ];
                    }
                    break;
                case 'update':
                    $user = new UserRecord(strtolower($username), $password);
                    if( $user->readFromDb() ) {
                        $user->setRole($role);
                        $user->setPassword($password);
                        if( $user->update() ) {
                            return [ 'success' => true,
                                     'data' => UserController::userToArray($user) ];
                        } else {
                            return [ 'success' => false,
                                     'message' => $user->getMessages()[0] ];
                        }
                    } else {
                        return [ 'success' => false,
                                 'message' => "User $userId not found for update" ]; 
                    }
                    break;
                case 'delete':
                    $user = new UserRecord(strtolower($username), '');
                    if( $user->readFromDb() &&
                        $user->delete() ) {
                        return [ 'success' => true,
                                 'data' => UserController::userToArray($user) ];
                    } else {
                        return [ 'success' => false,
                                 'message' => $user->getMessages()[0] ];
                    }
                    break;
                default:
                    return [ 'success' => false,
                             'message' => "Unknown action " . htmlspecialchars($action) ];
            }
        } else {
            return [ 'success' => false, 'message' => "Nur Administratoren dürfen Benutzer verwalten." ];
        }
    }

    private static function login(): array {
        return [
            'success' => false,
            'message' => "Cannot login yet"
        ];
    }

    private static function logout(): array {
        $url = nl2br(\LaPlanner\getQueryString('url'));
        if($url==='') $url = './index.html';

        error_log("Url: " . $url);
        
        $version = \LaPlanner\getDbVersion();
        header("Location: $url");

        session_unset();
        session_destroy();
        setcookie(session_name(), '', 0, '/');
        return [
            'success' => false,
            'message' => "Cannot logout yet"
        ];
    }

    private static function currentUserCanManageUsers(): bool {
        $version = \LaPlanner\getDbVersion();
        return ( $version['userrole'] ==='superuser' ||  $version['userrole']==='admin' );
    }

    private static function userToArray(UserRecord $user): array {
        $result = [];
        $result['id'] = $user->getId();
        $result['name'] = $user->getName();
        $result['role'] = $user->getRole();
        return $result;       
    }
}
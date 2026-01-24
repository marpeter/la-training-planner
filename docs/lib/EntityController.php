<?php
namespace TnFAT\Planner;

const ENTITIES = ['Discipline', 'Exercise', 'Favorite'];
const POST_ACTIONS = ['create', 'update', 'delete'];

use TnFAT\Planner\AbstractEntityReader as EntityReader;

class EntityController {
    public static function handle(array $pathTokens): void {

        if (count($pathTokens) < 1) {
            echo json_encode([
                'success' => false,
                'message' => 'Missing entity in request URI.',
            ]);
            exit;
        }

        $entity = ucfirst($pathTokens[0]);
        if(!in_array($entity, ENTITIES)) {
            echo json_encode([
                'success' => false,
                'message' => 'You must specify a valid entity instead of: ' . htmlspecialchars($entity),
            ]);
            exit;
        }

        $entityId = null;
        if(count($pathTokens) > 1 && !str_starts_with($pathTokens[1], 'format')) {
            $entityId = filter_var($pathTokens[1], FILTER_SANITIZE_STRING, FILTER_NULL_ON_FAILURE);
        }

        switch ($_SERVER['REQUEST_METHOD']) {

            case 'GET':
                $format = strtolower(\LaPlanner\getQueryString('format'));
                $reader = EntityReader::getReader($entity, $format);
                echo $reader->read($entityId);
                break;

            case 'POST':
                if (!in_array($entity, ENTITIES)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'You must specify a valid entity instead of: ' . htmlspecialchars($entity),
                    ]);
                    exit;
                }
                $action = strtolower(\LaPlanner\getPostedString('verb'));
                if (!in_array($action, POST_ACTIONS)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'You must specify a valid verb instead of: ' . htmlspecialchars($action),
                    ]);
                    exit;
                }
                $saverClass = "\\TnFAT\\Planner\\$entity\\DatabaseWriter";
                $saver = new $saverClass();
                echo json_encode(
                    $saver->$action(json_decode($_POST['data'],true))
                );
                break;

            default:
                http_response_code(405);
                echo "Unsupported HTTP method: " . htmlspecialchars($_SERVER['REQUEST_METHOD']);
                exit;
        }
    }
}
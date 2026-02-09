<?php
namespace TnFAT\Planner;

const ENTITIES = ['Discipline', 'Exercise', 'Favorite'];
const POST_ACTIONS = ['create', 'update', 'delete'];

use TnFAT\Planner\EntityFormatterFactory;
use TnFAT\Planner\RequestException;
use TnFAT\Planner\Utils;

class EntityController {
    public static function handle(array $pathTokens): string {

        if (count($pathTokens) < 1) {
            throw new RequestException('Missing entity in request URI.', 400);
        }

        $entity = ucfirst($pathTokens[0]);
        if(!in_array($entity, ENTITIES)) {
            throw new RequestException('You must specify a valid entity instead of: ' . htmlspecialchars($entity), 400);
        }

        // if the URL has the form /entity/{id}[?format=], then extract the id
        $entityId = null;
        if(count($pathTokens) > 1 && !str_starts_with($pathTokens[1], 'format')) {
            $entityId = filter_var($pathTokens[1], FILTER_SANITIZE_STRING, FILTER_NULL_ON_FAILURE);
        }

        switch ($_SERVER['REQUEST_METHOD']) {

            case 'GET':
                $format = strtolower(Utils::getQueryString('format'));
                $reader = EntityFormatterFactory::getReader($entity, $format);
                return $reader->read($entityId);;

            case 'POST':
                if (!in_array($entity, ENTITIES)) {
                    throw new RequestException('You must specify a valid entity instead of: ' . htmlspecialchars($entity), 400);
                }

                $action = strtolower(Utils::getPostedString('verb'));
                if (!in_array($action, POST_ACTIONS)) {
                    throw new RequestException('You must specify a valid verb instead of: ' . htmlspecialchars($action), 400);
                }

                $saverClass = "\\TnFAT\\Planner\\$entity\\DatabaseTable";
                $saver = new $saverClass();
                return json_encode(
                    $saver->$action(json_decode($_POST['data'],true))
                );
                break;

            default:
                throw new RequestException("Unsupported HTTP method: " . htmlspecialchars($_SERVER['REQUEST_METHOD']), 405);
        }
    }
}
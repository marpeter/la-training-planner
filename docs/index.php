<?php
namespace TnFAT\Planner;
  
require_once __DIR__ . '/lib/autoload.php';
use TnFAT\Planner\RequestException;
use TnFAT\Planner\Utils;

$pathTokens = [];
$token = strtok(
    substr($_SERVER['REQUEST_URI'], strlen('/la-planer/index.php/')),
    '/?');
while ($token !== false) {
    $pathTokens[] = $token;
    $token = strtok('/?');
}

try {
    switch ( strtolower(array_shift($pathTokens)) ) {
        case 'entity':
            echo EntityController::handle($pathTokens);
            break;

        case 'user': // not treated like a regular entity because access needs
                     // protection and passwords special treatment
            echo UserController::handle($pathTokens); 
            break;

        case 'version': // not treated like a regular entity
            $version = Utils::getSessionInfo();
            echo json_encode($version);
            break;

        default:
            throw new RequestException("Invalid request URI: " . htmlspecialchars($_SERVER['REQUEST_URI']), 400);
    }
} catch( RequestException $ex ) {
    http_response_code($ex->getCode());
    echo $ex->getMessage();
}

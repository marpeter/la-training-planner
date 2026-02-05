<?php
namespace TnFAT\Planner;
  
require_once __DIR__ . '/lib/autoload.php';
use TnFAT\Planner\Utils;

$pathTokens = [];
$token = strtok(
    substr($_SERVER['REQUEST_URI'], strlen('/la-planer/index.php/')),
    '/?');
while ($token !== false) {
    $pathTokens[] = $token;
    $token = strtok('/?');
}

switch ( strtolower(array_shift($pathTokens)) ) {
    case 'entity':
        EntityController::handle($pathTokens);
        break;

    case 'user': // not treated like a regular entity because access needs
                 // protection and passwords special treatment
        UserController::handle($pathTokens); 
        break;

    case 'version': // not treated like a regular entity
        $version = Utils::getDbVersion();
        echo json_encode($version);
        break;

    default:
        http_response_code(400);
        echo "Invalid request URI: " . htmlspecialchars($_SERVER['REQUEST_URI']);
        exit;
}
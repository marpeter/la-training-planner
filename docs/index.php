<?php
namespace TnFAT\Planner;
  
require_once __DIR__ . '/lib/autoload.php';
require 'data/db_common.php';

use \TnFAT\Planner\EntityController as EntityController;

$pathTokens = [];
$token = strtok(
    substr(
        strtolower($_SERVER['REQUEST_URI']), strlen('/la-planer/index.php/')),
    '/?');
while ($token !== false) {
    $pathTokens[] = $token;
    $token = strtok('/?');
}

// error_log("Request URI tokens: " . var_export($pathTokens, true));

switch ($pathTokens[0]) {
    case 'entity':
        array_shift($pathTokens);
        EntityController::handle($pathTokens);
        break;

    default:
        http_response_code(400);
        echo "Invalid request URI: " . htmlspecialchars($_SERVER['REQUEST_URI']);
        exit;
}
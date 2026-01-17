<?php
namespace LaPlanner;

require '../data/db_common.php';

$reader = null;
$entity = strtolower(getQueryString('entity'));
switch($entity) {
    case 'disciplines':
        $reader = new \TnFAT\Planner\Discipline\CsvReader();
        break;
    case 'exercises':
        $reader = new \TnFAT\Planner\Exercise\CsvReader();
        break;
    case 'favorites':
        $reader = new \TnFAT\Planner\Favorite\CsvReader();
        break;
    default:
        http_response_code(400);
        echo "Unknown entity: " . htmlspecialchars($entity);
        exit;
}

$reader->echo();
<?php
namespace LaPlanner;
  
require 'db_common.php';

use \TnFAT\Planner\Discipline\DatabaseReader as DisciplineReader;
use \TnFAT\Planner\Exercise\DatabaseReader as ExerciseReader;
use \TnFAT\Planner\Favorite\DatabaseReader as FavoriteReader;

$reader = null;
switch(strtolower($_GET['entity'])) {
    case 'disciplines':
        $reader = new DisciplineReader();
        break;
    case 'exercises':
        $reader = new ExerciseReader();
        break;
    case 'favorites':
        $reader = new FavoriteReader();
        break;
    default:
        http_response_code(400);
        echo "Unknown entity: " . htmlspecialchars($_GET['entity']);
        exit;
}

$reader->echo();
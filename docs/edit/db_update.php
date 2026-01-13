<?php
namespace LaPlanner;

require '../data/db_write.php';

$SAVERS = ['exercise' => 'LaPlanner\ExerciseSaver', 'favorite' => 'LaPLanner\FavoriteSaver'];
$VERBS = ['create', 'update', 'delete'];

if( !( isset($_POST['entity']) && is_string($_POST['entity']) && isset($SAVERS[$_POST['entity']]) ) ) {
    echo json_encode([
        'success' => false,
        'message' => 'You must specify a valid entity',
    ]);
    exit;
} else {
    $saver = new $SAVERS[$_POST['entity']]();
}
if( !( isset($_POST['verb']) && is_string($_POST['verb']) && in_array($_POST['verb'],$VERBS) ) ) {
   echo json_encode([
        'success' => false,
        'message' => 'You must specify a valid verb (create, update or delete): ' . htmlspecialchars($_POST['verb']),
    ]);
    exit;
}
if( !isset($_POST['data']) ) {
   echo json_encode([
        'success' => false,
        'message' => 'You must specify data',
    ]);
    exit;
}

$action = getPostedString('verb');
echo json_encode(
    $saver->$action(
        json_decode($_POST['data'],true)
));
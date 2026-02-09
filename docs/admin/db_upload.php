<?php
namespace LaPlanner;

require_once __DIR__ . '/../lib/autoload.php';

use \TnFAT\Planner\Utils;
use \TnFAT\Planner\Discipline\CsvLoader as DisciplineLoader;
use \TnFAT\Planner\Exercise\CsvLoader as ExerciseLoader;
use \TnFAT\Planner\Favorite\CsvLoader as FavoriteLoader;

$messages = [];
$uploadedFiles = 0;
  
function readFromFile($realFileName,$logicalFileName, &$messages): array|false {
    $handle = fopen($realFileName, 'r');
    if($handle) {
        $content = [];
        while(($buffer = fgets($handle, 4096)) !== false) {
            $content[] = $buffer;
        }
        fclose($handle);
        return $content;
    } else {  
        $messages[] = "Die hochgeladene Datei $logicalFileName kann nicht geöffnet werden.";
        return false;
    }
}

$workItem = [];
$uploadedFiles = 0;
if ( !empty($_FILES) ) { // files were uploaded
    $allowed_mimetypes = [
        'text/csv' => 'csv',
        'text/plain' => 'txt',
        'text/tab-separated-values' => 'csv'
    ];
    foreach($_FILES as $file => $data) {
        $name = htmlspecialchars($data['name']);
        switch ($data['error']) {
            case UPLOAD_ERR_OK:     
                $type = mime_content_type($data['tmp_name']);
                if(is_uploaded_file($data['tmp_name']) === false) {
                    $messages[] = "Die Datei $name wurde nicht korrekt hochgeladen.";
                    break;
                }
                if(isset($allowed_mimetypes[$type])) {
                    switch ($file) {
                        case 'DisciplinesFile':
                            $workItem[] = [$data['name'], $data['tmp_name'], new DisciplineLoader()];
                            break;
                        case 'ExercisesFile':
                            $workItem[] = [$data['name'], $data['tmp_name'], new ExerciseLoader()];
                            break;
                        case 'FavoritesFile':
                            $workItem[] = [$data['name'], $data['tmp_name'], new FavoriteLoader()];
                            break;
                        default:
                            $messages[] = "Unbekannte Art von Daten {$file} übermittelt.";
                            break;
                    }
                } else {
                    $messages[] = "Die hochgeladene Datei $name ist keine Text/CSV-Datei.";
                }  
                break;
            case UPLOAD_ERR_INI_SIZE:
                $messages[] = "Die Datei $name überschreitet die maximal erlaubte Größe.";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $messages[] = "Die Datei  $name überschreitet die maximal erlaubte Größe.";
                break;
            case UPLOAD_ERR_NO_FILE:
                // No error if one the other files was uploaded
                break;
            default:
                $messages[] = 'Upload der Dateien fehlgeschlagen.';
                break;
        }
    }
} elseif( isset($_POST['uploadIncludedData']) && $_POST['uploadIncludedData'] === '1' ) {
    // Upload the files that are included with the app
    $workItem = [
        ['Disciplines.csv', '../data/Disciplines.csv', new DisciplineLoader()],
        ['Exercises.csv', '../data/Exercises.csv', new ExerciseLoader()],
        ['Favorites.csv', '../data/Favorites.csv', new FavoriteLoader()]];
} 

if( !empty($workItem) ) { 
    foreach($workItem as [$logicalFileName, $realFileName, $loader]) {
        $content = readFromFile($realFileName, $logicalFileName, $messages);
        if( $loader !== null ) { $uploadedFiles += $loader->load($content, $messages); }
    }
    if( $uploadedFiles === 0 ) {
        $messages[] = "Es wurden keine (gültigen) Daten hochgeladen.";
    } else {
        $messages[] = "Es wurden $uploadedFiles gültige Daten hochgeladen.";
    }
}

$version = Utils::getSessionInfo();

if( isset($version['username']) ) {
  $icon = 'logout';
  $loginButtonHref = "../index.php/user/logout?url=http" .
    (isset($_SERVER['HTTPS']) ? "s" : "" ) . "://" . 
    $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . $_SERVER['PHP_SELF'];
} else {
  $icon = 'login';
  $loginButtonHref = "../login.html";
}

<?php
namespace TnFAT\Planner;

require_once __DIR__ . '/../lib/autoload.php';

use TnFAT\Planner\Utils;
use TnFAT\Planner\Setup\DataLoader;

$messages = [];
$workItems = [];
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
                    if(in_array($file, ['DisciplinesFile', 'ExercisesFile', 'FavoritesFile'])) {
                        $workItems[] = [str_replace('sFile','', $file), $data['name'], $data['tmp_name']];
                    } else {
                        $messages[] = "Unbekannte Art von Daten {$file} übermittelt.";
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
    $workItems = DataLoader::ALL;
} 

if( !empty($workItems) ) {
    $loader = new DataLoader($workItems);
    $uploadedFiles = $loader->load();
    $messages = $loader->getMessages();
}

[$version, $user] = Utils::getSessionInfo();

if( isset($user['name']) ) {
  $icon = 'logout';
  $loginButtonHref = "./index.php/user/logout?url=http" .
    (isset($_SERVER['HTTPS']) ? "s" : "" ) . "://" . 
    $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . $_SERVER['PHP_SELF'];
} else {
  $icon = 'login';
  $loginButtonHref = "./login.html";
}

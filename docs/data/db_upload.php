<!DOCTYPE html>
<html>
  <head>
    <title>Hilfs-Funktionen für den Planer für Leichtathletik-Trainings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="icon" type="image/x-icon" href="../assets/tsvlogo.png">
    <link rel="apple-touch-icon" type="image/png" href="../assets/tsvlogo.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet"
           href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
  </head>
  <body>
    <div class="container">
      <nav class="red darken-1">
        <div class="nav-wrapper">
          <a href="#" class="brand-logo left"><img src="../assets/tsvlogo.png" height="50px"/></a>
          <ul id="nav-mobile" class="right">
            <li><a href="../help.html" class="btn-small red"><i class="material-icons">help_center</i></a></li>
            <li><a href="../edit.html" id="editBtn" class="btn-small red disabled"><i class="material-icons">edit</i></a></li>
            <li><a href="../index.html" class="btn-small red"><i class="material-icons">home</i></a></li>
          </ul>
        </div>
      </nav>
<?php

  include('db_connect.php');
  include('db_load_disciplines.php');
  include('db_load_exercises.php');
  include('db_load_favorites.php');
 
  $messages = [];
  $uploadedFiles = 0;

  $allowed_mimetypes = [
    'text/csv' => 'csv',
    'text/plain' => 'txt',
    'text/tab-separated-values' => 'csv'
  ];
  
  if (!empty($_FILES)) {
    foreach($_FILES as $file => $data) {
      switch ($data['error']) {
        case UPLOAD_ERR_OK:     
          $type = mime_content_type($data['tmp_name']);
          if(isset($allowed_mimetypes[$type])) {
            $handle = fopen($data['tmp_name'], 'r');
            if($handle) {
              $content = [];
              while(($buffer = fgets($handle, 4096))!==false) {
                $content[] = $buffer;
              }
              fclose($handle);
              switch ($file) {
                case 'DisciplinesFile':
                  $uploadedFiles += loadDisciplines($content, $messages);  
                  break;

                case 'ExercisesFile':
                  $uploadedFiles += loadExercises($content, $messages);
                  break;

                case 'FavoritesFile':
                  $uploadedFiles += loadFavorites($content, $messages);
                  break;

                default:
                  $messages[] = "Unbekannte Art von Daten {$file} übermittelt.";
                  break;
              }
            } else {
              $messages[] = "Die hochgeladene Datei {$data['name']} kann nicht geöffnet werden.";
            }

          } else {
            $messages[] = "Die hochgeladene Datei {$data['name']} ist keine Text/CSV-Datei.";
          }  
          break;
        
        case UPLOAD_ERR_INI_SIZE:
          $messages[] = "Die Datei {$data['name']} überschreitet die maximal erlaubte Größe.";
          break;
      
        case UPLOAD_ERR_FORM_SIZE:
          $messages[] = "Die Datei  {$data['name']} überschreitet die maximal erlaubte Größe.";
          break;
      
        case UPLOAD_ERR_NO_FILE:
          // No error if one the other files was uploaded
          break;
      
        default:
          $messages[] = 'Upload der Datei fehlgeschlagen.';
          break;
      }  
    }
    if($uploadedFiles==0) {
      $messages[] = "Es wurden keine (gültigen) Daten hochgeladen.";
    } else {
      $messages[] = "Es wurden $uploadedFiles gültige Daten hochgeladen.";
    }
  } else {
    $messages[] = "Es wurden keine (gültigen) Daten hochgeladen.";
  }
  $version = getDbVersion();
?>

      <div class="row center">
        <div id="messages" class="red-text">
        <?php if(empty($messages)) { echo 'Datei wurde erfolgreich hochladen.'; } else { ?>
        <ul>
          <?php foreach($messages as $message) { ?>
          <li><?= htmlspecialchars($message) ?></li>
          <?php } ?>
        </ul>
        <?php } ?>    
        </div>
      </div>
      <div class="row center">
        <div class="col s6"><a class="red-text" href="../index.html">Zurück zur Trainingsplaner-Seite</a></div>
        <div class="col s6"><a class="red-text" href="../admin.html">Zurück zur Admin-Seite</a></div>
      </div>
    </div>
    <footer class="section">
      <div class="center grey-text">Version <span id="version"><?php echo $version['number'] ?></span>,<br/>&copy; 2025 Markus Peter</div>
    </footer>
  </body>
</html>
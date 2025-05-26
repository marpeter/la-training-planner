<?php
  namespace LaPlanner;

  include('../data/db_common.php');
  include('db_upload_loaders.php');
?>
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
            <li><a href="../help/" class="btn-small red"><i class="material-icons">help_center</i></a></li>
            <li><a href="../edit/" id="editBtn" class="btn-small red disabled"><i class="material-icons">edit</i></a></li>
            <li><a href="../" class="btn-small red"><i class="material-icons">home</i></a></li>
          </ul>
        </div>
      </nav>
      <div class="section">
        <h4 class="card-panel red darken-1 center">Hilfsfunktionen für den LA Trainings-Planer</h4>    
      </div>
      <div class="row">
        <div class="col s12">Download der Daten aus der Datenbank:</div>
      </div> 
      <div class="row">
        <div class="col s6"> <a id="downloadDisciplinesBtn" href="db_download.php?entity=Disciplines" class="btn center red">Disziplinen</a></div>
        <div class="col s6"> <a id="downloadExercisesBtn" href="db_download.php?entity=Exercises" class="btn center red">Übungen</a></div>
      </div>
      <div class="row">
        <div class="col s12"> <a id="downloadFavoritesBtn" href="db_download.php?entity=Favorites" class="btn center red">Gespeicherte Pläne (Favoriten)</a></div> 
      </div>
      <div class="divider"></div>
      <div class="row">
        <div class="col s12">Upload von Daten in die Datenbank:
        <div class="card-panel red center">Achtung! Dies ersetzt die bereits in der Datenbank gespeicherten Daten vollständig!</div></div>
      </div>
      <form name="uploadForm" enctype="multipart/form-data" method="POST" action="#">
        <div class="row">
          <div class="col s6">
            <label>Wähle eine CSV-Datei mit der Liste der Disziplinen:
            <input type="file" id="DisciplinesFile" name="DisciplinesFile" accecpt="text/csv,text/plain">
            </label>
          </div>
          <div class="col s6">
            <label>Wähle eine CSV-Datei mit der Liste der Übungen:
            <input type="file" id="ExercisesFile" name="ExercisesFile" accecpt="text/csv,text/plain">
            </label>
          </div>
        </div>
        <div class="row">
          <div class="col s6">
            <label>Wähle eine CSV-Datei mit der Liste der Favoriten:
            <input type="file" id="FavoritesFile" name="FavoritesFile" accecpt="text/csv,text/plain">
            </label>
          </div>
          <div class="col s6"> <button id="uploadDataBtn" class="btn center red">Daten hochladen</button></div> 
        </div>
      </form>
      <form name="uploadIncludedData" method="POST" action="#">
        <div class="row">
          <div class="col s12 center">
            <input type="hidden" name="uploadIncludedData" value="1">
            <button id="uploadIncludedDataBtn" class="btn center red">Mit der Appp gelieferte Daten hochladen</button>
          </div>
        </div>
      </form>    
<?php
 
  $messages = [];
  $uploadedFiles = 0;
  
  function readFromFile($realFileName,$logicalFileName, &$messages) {
    $handle = fopen($realFileName, 'r');
    if($handle) {
      $content = [];
      while(($buffer = fgets($handle, 4096))!==false) {
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
  if (!empty($_FILES)) { // files were uploaded
    $allowed_mimetypes = [
      'text/csv' => 'csv',
      'text/plain' => 'txt',
      'text/tab-separated-values' => 'csv'
    ];
    foreach($_FILES as $file => $data) {
      switch ($data['error']) {
        case UPLOAD_ERR_OK:     
          $type = mime_content_type($data['tmp_name']);
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
          $messages[] = 'Upload der Dateien fehlgeschlagen.';
          break;
      }  
    }
  } elseif (isset($_POST['uploadIncludedData']) && $_POST['uploadIncludedData'] == '1') {
    // Upload the files that are included with the app
    $workItem = [
      ['Disciplines.csv', '../data/Disciplines.csv', new DisciplineLoader()],
      ['Exercises.csv', '../data/Exercises.csv', new ExerciseLoader()],
      ['Favorites.csv', '../data/Favorites.csv', new FavoriteLoader()]];
  } 

  if(!empty($workItem)) { 
    foreach($workItem as [$logicalFileName, $realFileName, $loader]) {
      $content = readFromFile($realFileName, $logicalFileName, $messages);
      if($loader!=null) { $uploadedFiles += $loader->load($content, $messages); }
    }
    if($uploadedFiles==0) {
      $messages[] = "Es wurden keine (gültigen) Daten hochgeladen.";
    } else {
      $messages[] = "Es wurden $uploadedFiles gültige Daten hochgeladen.";
    }
  }

  $version = getDbVersion();
?>
      <div class="row center">
        <div id="messages" class="red-text">
        <?php if(!empty($messages)) { ?>
        <ul>
          <?php foreach($messages as $message) { ?>
          <li><?= htmlspecialchars($message) ?></li>
          <?php } ?>
        </ul>
        <?php } ?>    
        </div>
      </div>
    </div>
    <footer class="section">
      <div class="center grey-text">Version <span id="version"><?php echo $version['number'] ?></span>,<br/>&copy; 2025 Markus Peter</div>
    </footer>
  </body>
</html>
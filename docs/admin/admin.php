<?php
require 'db_upload.php';
?>
<!doctype html>
<html lang="de">
  <head>
    <title>Hilfs-Funktionen für den Planer für Leichtathletik-Trainings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="icon" type="image/x-icon" href="../assets/logo.png">
    <link rel="apple-touch-icon" type="image/png" href="../assets/logo.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet"
           href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="../theme/theme.css">
  </head>
  <body>
    <header>
      <nav class="tfat darken-1">
        <div class="nav-wrapper">
          <a href="#" class="brand-logo left"><img src="../assets/logo.png" alt="Logo" class="tfat-logo">Einrichtung</a>
          <ul class="right">
            <li><a href="<?= $loginButtonHref ?>" id="loginBtn"
              class="btn-small tfat"><i class="material-icons"><?= $icon ?></i></a></li>
          </ul>
        </div>
      </nav>
    </header>
    <section class="container">
      <h4 class="center">Download der Daten aus der Datenbank</h4> 
      <!-- <p>Download der Daten aus der Datenbank:</p> -->
      <div class="row">
        <div class="col s6"> <a id="downloadDisciplinesBtn" href="../index.php/entity/discipline/?format=csv" class="btn center tfat">Disziplinen</a></div>
        <div class="col s6"> <a id="downloadExercisesBtn" href="../index.php/entity/exercise/?format=csv" class="btn center tfat">Übungen</a></div>
      </div>
      <div class="row">
        <div class="col s12"> <a id="downloadFavoritesBtn" href="../index.php/entity/favorite/?format=csv" class="btn center tfat">Gespeicherte Pläne (Favoriten)</a></div> 
      </div>
    </section>
    <div class="divider"></div>
    <section class="container">
      <h4 class="center">Upload von Daten in die Datenbank:</h4>
      <div class="card-panel tfat-warning center">Achtung! Dies ersetzt die bereits in der Datenbank gespeicherten Daten vollständig!
        Daher steht die Funktion nur Benutzern mit Administrationsrechten zur Verfügung.</div>
      <form name="uploadForm" enctype="multipart/form-data" method="POST" action="#">
        <div class="row">
          <div class="col s6">
            <?php $disabled = $version['supportsEditing'] ? '' : 'disabled' ?> 
            <label>Wähle eine CSV-Datei mit der Liste der Disziplinen:
              <input type="file" id="DisciplinesFile" name="DisciplinesFile" accecpt="text/*" <?= $disabled ?>>
            </label>
          </div>
          <div class="col s6">
            <label>Wähle eine CSV-Datei mit der Liste der Übungen:
            <input type="file" id="ExercisesFile" name="ExercisesFile" accecpt="text/*" <?= $disabled ?>>
            </label>
          </div>
        </div>
        <div class="row">
          <div class="col s6">
            <label>Wähle eine CSV-Datei mit der Liste der Favoriten:
            <input type="file" id="FavoritesFile" name="FavoritesFile" accecpt="text/*" <?= $disabled ?>>
            </label>
          </div>
          <div class="col s6"> <button id="uploadDataBtn" class="btn center tfat <?= $disabled ?>">Daten hochladen</button></div> 
        </div>
      </form>
      <form name="uploadIncludedData" method="POST" action="#">
        <div class="row">
          <div class="col s12 center">
            <input type="hidden" name="uploadIncludedData" value="1">
            <button id="uploadIncludedDataBtn" class="btn center tfat <?= $disabled ?>">Mit der App gelieferte Daten hochladen</button>
          </div>
        </div>
      </form> 
      <div class="row center">
        <div id="messages" class="teal-text">
        <?php if(!empty($messages)) { ?>
        <ul>
          <?php foreach($messages as $message) { ?>
          <li><?= htmlspecialchars($message) ?></li>
          <?php } ?>
        </ul>
        <?php } ?>    
        </div>
      </div>
    </section>
    <div class="divider"></div>
    <div class="center grey-text">Version <span id="version"><?php echo $version['number'] ?></span>, &copy; 2026 Markus Peter</div>
    <footer>
      <nav class="tfat darken-1">
        <div class="nav-wrapper">
          <ul>
            <li><a href="../" class="btn-small tfat"><i class="material-icons">home</i></a></li>
            <li><a href="../help.html" class="btn-small tfat"><i class="material-icons">help_center</i></a></li> 
            <li><a href="../edit.html" id="menuItemEdit" class="btn-small tfat"><i class="material-icons">construction</i></a></li>
            <li class="active"><a href="./" class="btn-small tfat"><i class="material-icons">import_export</i></a></li>
          </ul>
        </div>
      </nav>
    </footer>
  </body>
</html>
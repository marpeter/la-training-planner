<?php include('db_upload.php'); ?>

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
    <header>
      <nav class="red darken-1">
        <div class="nav-wrapper">
          <a href="#" class="brand-logo left"><img src="../assets/tsvlogo.png" height="50px"/></a>
          <ul id="nav-mobile" class="right">
            <li><a href="../help/" class="btn-small red"><i class="material-icons">help_center</i></a></li>
            <li><a href="../edit/" class="btn-small red"><i class="material-icons">edit</i></a></li>
            <li><a href="../" class="btn-small red"><i class="material-icons">home</i></a></li>
          </ul>
        </div>
      </nav>
    </header>
    <section class="container">
      <h4 class="card-panel red darken-1 center">Hilfsfunktionen für den LA Trainings-Planer</h4> 
      <p>Download der Daten aus der Datenbank:
        <div class="row">
          <div class="col s6"> <a id="downloadDisciplinesBtn" href="db_download.php?entity=Disciplines" class="btn center red">Disziplinen</a></div>
          <div class="col s6"> <a id="downloadExercisesBtn" href="db_download.php?entity=Exercises" class="btn center red">Übungen</a></div>
        </div>
        <div class="row">
          <div class="col s12"> <a id="downloadFavoritesBtn" href="db_download.php?entity=Favorites" class="btn center red">Gespeicherte Pläne (Favoriten)</a></div> 
        </div>
      </p>
    </section>
    <div class="divider"></div>
    <section class="container">
      <p>Upload von Daten in die Datenbank:
        <div class="card-panel red center">Achtung! Dies ersetzt die bereits in der Datenbank gespeicherten Daten vollständig!</div>
      </p>
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
            <button id="uploadIncludedDataBtn" class="btn center red">Mit der App gelieferte Daten hochladen</button>
          </div>
        </div>
      </form> 
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
    </section>
    <div class="divider"></div>
    <footer class="section">
      <div class="center grey-text">Version <span id="version"><?php echo $version['number'] ?></span>,<br/>&copy; 2025 Markus Peter</div>
    </footer>
  </body>
</html>
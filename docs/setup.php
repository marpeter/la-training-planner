<?php
require 'lib/SetupController.php';
?>
<!doctype html>
<html lang="de">
  <head>
    <title>Installation des Planers für Leichtathletik-Trainings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="icon" type="image/x-icon" href="./assets/logo.png">
    <link rel="apple-touch-icon" type="image/png" href="./assets/logo.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet"
           href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <!-- Compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link rel="stylesheet" href="./theme/theme.css">
  </head>
  <body>
    <header>
      <nav class="tfat darken-1">
        <div class="nav-wrapper">
          <a href="#" class="brand-logo left"><img src="./assets/logo.png" alt="Logo" class="tfat-logo">Installation</a>
          <ul class="right">
            <li><a href="./login.html" id="loginBtn"
              class="btn-small tfat"><i class="material-icons">login</i></a></li>
          </ul>
        </div>
      </nav>
    </header>
    <main class="container">
      <section>
        <h4 class="center">Installation der Trainings-Planer App abschließen</h4>
        <?php if( $version['withDB']) { ?>
          <div class="card-panel tfat-warning center">Achtung! Die Installation der App ist bereits abgeschlossen.<br>
            Möchtest Du die Installation wirklich erneut durchführen? <br>
            Dies überschreibt die bestehende Datenbank und alle darin gespeicherten Daten werden gelöscht!
          </div>
        <?php } ?>
        <form name="installForm" method="post" action="#">
          <div class="row">
            <div class="col s12 l6 offset-l3">Lege zur Fertigstellung der Trainings-Planer App Installation
              einen Super-User für die App selbst an:
            </div>
          </div>
          <div class="row">
            <div class="input-field col s12 l6 offset-l3">
              <input type="text" id="su_name" name="su_name"
                value="<?= htmlspecialchars($_POST['su_name'] ?? '') ?>">
              <label for="su_name">Name des "Superusers" der App</label>
            </div>
          </div>
          <div class="row">
            <div class="input-field col s12 l6 offset-l3">
              <input type="password" id="su_password" name="su_password"
                value="<?= htmlspecialchars($_POST['su_password'] ?? '') ?>">
              <label for="su_password">Superuser Passwort</label>
            </div>
          </div>
          <?php if( count($suMessages) > 0 ) { ?>
          <div class="row">
            <div class="col s12 l6 offset-l3 tfat">
              <?php
                foreach($suMessages as $message) {
                  echo htmlspecialchars($message) . "<br>";
                }
              ?> 
            </div>
          </div>
          <?php } ?>
          <div class="row">
            <div class="col s12 l6 offset-l3">Gib Name und Passwort eines
              Datenbankadministrators (z.B. 'root' ) an, mit dem sich die App
              zur Installation mit der Datenbank verbinden kann. Dieser
              Datenbank-Benutzer wird <strong>nach</strong> der Installation
              nicht mehr verwendet. Stattdessen wird während der Installation
              ein neuer Datenbank-Benutzer <strong>tfat_planner</strong> mit
              einem generierten Passwort angelegt.<br>
              Alternativ gib den Namen und Passwort eines Datenbank-Benutzers
              an, der bereits die notwendigen Rechte zum Anlegen der Tabellen in
              <strong>genau einer</strong> bereits vorhandenen (am besten leeren!)
              Datenbank besitzt.<br>
              Name und Passwort des Datenbank-Benutzers, den die App nach der
              Installation zur Verbindung mit der Datenbank verwendet, werden
              in Datei <code>config/config.php</code> gespeichert.<br>
            </div>
          </div>
          <div class="row">
            <div class="input-field col s12 l6 offset-l3">
              <input type="text" id="db_name" name="db_name"
                value="<?= htmlspecialchars($_POST['db_name'] ?? '') ?>">
              <label for="db_name">Datenbank-Benutzer</label>
            </div>
          </div>
          <div class="row">
            <div class="input-field col s12 l6 offset-l3">
              <input type="password" id="db_password" name="db_password"
                value="<?= htmlspecialchars($_POST['db_password'] ?? '') ?>">
              <label for="db_password">Datenbank-Passwort</label>
            </div>
          </div>
          <?php if( count($dbMessages) > 0 ) { ?>
          <div class="row">
            <div class="col s12 l6 offset-l3 tfat">
              <?php
                foreach($dbMessages as $message) {
                  echo htmlspecialchars($message) . "<br>";
                }
              ?> 
            </div>
          </div>
          <?php } ?>
          <div class="row">            
              <div class="col s12 l6 offset-l3">
                <button id="installBtn" name='action' value='setusers' class="btn center tfat">Installation abschließen</button>
            </div>
          </div>   
        </form>
      </section>
    </main>
    <div class="divider"></div>
    <div class="center grey-text">Version <span id="version"><?php echo $version['number'] ?></span>, &copy; 2026 Markus Peter</div>
    <footer>
      <nav class="tfat darken-1">
        <div class="nav-wrapper">
          <ul>
            <li><a href="./" class="btn-small tfat"><i class="material-icons">home</i></a></li>
            <li><a href="./help.html" class="btn-small tfat"><i class="material-icons">help_center</i></a></li> 
            <li><a href="./edit.html" id="menuItemEdit" class="btn-small tfat disabled"><i class="material-icons">construction</i></a></li>
            <li class="active"><a href="./import-export.html" class="btn-small tfat"><i class="material-icons">import_export</i></a></li>
          </ul>
        </div>
      </nav>
    </footer>
  </body>
</html>
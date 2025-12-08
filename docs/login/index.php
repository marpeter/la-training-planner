<?php
    namespace LaPlanner;
    include('../data/db_common.php');
    $version = getDbVersion(true);

    if( isset($version['username']) ) {

    } elseif( isset($_POST['username']) && isset($_POST['password']) ) {
      $userName = $_POST['username'];
      $password = $_POST['password'];

      // TODO: check username and password for validity ...

      $version['username'] = $userName;
      $_SESSION['username'] = $userName; 
    }

    if( isset($version['username']) ) {
      $icon = 'logout';
      $disabled = 'disabled';
      $loginButtonHref = "logout.php?url=./";
    } else {
      $icon = 'login';
      $disabled = '';
      $loginButtonHref = "#";
    }
?>
<!doctype html>
<html lang="de">
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
    <link rel="stylesheet" href="../ownstyle.css">
  </head>
  <body>
    <header>
      <nav class="red darken-1">
        <div class="nav-wrapper">
          <a href="#" class="brand-logo left"><img src="../assets/tsvlogo.png" alt="Logo" height="50" class="left">Einrichtung</a>
          <ul class="right">
            <li class="active"><a href="<?= $loginButtonHref ?>" id="loginBtn"
              class="btn-small red"><i class="material-icons"><?= $icon ?></i></a></li>
          </ul>
        </div>
      </nav>
    </header>
    <section class="container">
      <h4 class="center">Als Benutzer anmelden</h4>
      <p>
        Als angemeldeter Benutzer kannst du eigene Favoriten speichern, ändern und löschen.
        Je nach Berechtigung kannst du auch Übungen kopieren, ändern oder löschen und Daten in die Datenbank laden.
      </p>
      <form name="loginForm" method="post" action="#">
        <div class="row">
          <div class="col s12 l6 offset-l3">
            <label>Benutzername:
              <input type="text" id="userName" name="username" <?= $disabled ?>>
            </label>
          </div>
        </div>
        <div class="row">
          <div class="col s12 l6 offset-l3">
            <label>Passwort:
              <input type="password" id="userName" name="password" <?= $disabled ?>>
            </label>
          </div>
        </div>
        <div class="row">
          <div class="col s12 l6 offset-l3">
            <input type="hidden" value="login" <?= $disabled ?>>
            <?php if( isset($version['username'])) { ?>
              Du bist angemeldet. Hallo <?= $version['username'] ?>.
              <a href="<?= $loginButtonHref ?>" class="btn red"><i class="material-icons right">logout</i>Abmelden</a>
            <?php } else { ?>
              <button id="loginBtn" class="btn center red">Anmelden</button>
            <?php } ?>
          </div>
        </div>                           
      </form>
    </section>
    <div class="divider"></div>
    <div class="center grey-text">Version <span id="version"><?php echo $version['number'] ?></span>, &copy; 2025 Markus Peter</div>
    <footer>
      <nav class="red darken-1">
        <div class="nav-wrapper">
          <ul>
            <li><a href="../" class="btn-small red"><i class="material-icons">home</i></a></li>
            <li><a href="../help/" class="btn-small red"><i class="material-icons">help_center</i></a></li> 
            <li><a href="../edit/" id="editBtn" class="btn-small red"><i class="material-icons">construction</i></a></li>
            <li><a href="../admin/" class="btn-small red"><i class="material-icons">import_export</i></a></li>
          </ul>
        </div>
      </nav>
    </footer>
  </body>
</html>
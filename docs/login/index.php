<?php
require 'login.php';
?>
<!doctype html>
<html lang="de">
  <head>
    <title>Benutzeranmeldung für den Planer für Leichtathletik-Trainings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="icon" type="image/x-icon" href="../assets/logo.png">
    <link rel="apple-touch-icon" type="image/png" href="../assets/logo.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet"
           href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <!-- Compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link rel="stylesheet" href="../ownstyle.css">
  </head>
  <body>
    <header>
      <nav class="red darken-1">
        <div class="nav-wrapper">
          <a href="#" class="brand-logo left"><img src="../assets/logo.png" alt="Logo" height="50" class="left">Benutzeranmeldung</a>
          <ul class="right">
            <li class="active"><a href="<?= $loginButtonHref ?>" id="loginBtn"
              class="btn-small red <?= $loginMenuItemDisabled ?>"><i class="material-icons"><?= $loggedIn ? 'logout' : 'login' ?></i></a></li>
          </ul>
        </div>
      </nav>
    </header>
    <main class="container">
      <section>
        <h4 class="center">Als Benutzer anmelden</h4>
        <p>
          Als angemeldeter Benutzer kannst du eigene Favoriten speichern, ändern und löschen.
          Je nach Berechtigung kannst du auch Übungen kopieren, ändern oder löschen und Daten in die Datenbank laden.
        </p>
        <form name="loginForm" method="post" action="#">
          <div class="row">
            <?php if( !$loggedIn) { ?>
              <div class="input-field col s12 l6 offset-l3">
                <input type="text" id="username" name="username" <?= $canLogin ?>>
                <label for="username">Benutzername</label>
              </div>
          <?php } else { ?>
            <div class="col s12 l6 offset-l3">
               Willkommen <?= $version['username'] ?>. Du bist angemeldet mit Rolle <?= $version['userrole'] ?>.
               <a href="<?= $loginButtonHref ?>" class="btn red"><i class="material-icons right">logout</i>Abmelden</a>
               <?php if( $canManageUsers ) { ?>
                <a href="users.php" class="btn center red">Benutzer verwalten
                   <i class="material-icons right">manage_accounts</i>
                </a>
              <?php } ?>
            </div>
          <?php } ?>
          </div>
          <div class="row">
            <div class="input-field col s12 l6 offset-l3">
              <input type="password" id="password" name="password" $canLogin>
              <label for="password">Passwort</label>
            </div>
          </div>
          <?php if( $loggedIn ) { ?>
            <div class="row">
              <div class="input-field col s12 l6 offset-l3">
                <input type="password" id="newpassword" name="new_password">
                <label for="newpassword">Neues Passwort</label>
              </div>
            </div>
          <?php } ?>
          <div class="row">            
              <div class="col s12 l6 offset-l3">
              <?php if( $loggedIn ) { ?>
                <button id="changePwdBtn" name='action' value='changePassword'class="btn center red">Passwort ändern</button>
              <?php } else { ?>
                <button id="loginBtn" name='action' value='login' class="btn center red <?= $canLogin ?>">Anmelden</button>
              <?php } ?>
            </div>
          </div>                           
        </form>
        <div class="row">
          <div class="col s12 l6 offset-l3 red">
            <?php
              foreach($messages as $message) {
                echo $message;
              }
            ?>
          </div>
        </div>
      </section>
    </main>
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
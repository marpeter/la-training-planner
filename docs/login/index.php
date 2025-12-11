<?php
    include('login.php');

    if( isset($version['username']) ) {
      $icon = 'logout';
      $disabled = 'disabled';
      $loginMenuItemDisabled = '';
      $loginButtonHref = "logout.php?url=./";
    } else {
      $icon = 'login';
      $disabled = $version['withDB'] === true ? '' : 'disabled';
      $loginMenuItemDisabled = $disabled;
      $loginButtonHref = "#";
    }

    $showCreateUserForm = (isset($version['userrole']) && $version['userrole']==='superuser');
?>
<!doctype html>
<html lang="de">
  <head>
    <title>Benutzeranmeldung für den Planer für Leichtathletik-Trainings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="icon" type="image/x-icon" href="../assets/tsvlogo.png">
    <link rel="apple-touch-icon" type="image/png" href="../assets/tsvlogo.png">
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
          <a href="#" class="brand-logo left"><img src="../assets/tsvlogo.png" alt="Logo" height="50" class="left">Benutzeranmeldung</a>
          <ul class="right">
            <li class="active"><a href="<?= $loginButtonHref ?>" id="loginBtn"
              class="btn-small red <?= $loginMenuItemDisabled ?>"><i class="material-icons"><?= $icon ?></i></a></li>
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
            <div class="input-field col s12 l6 offset-l3">
              <input type="text" id="userName" name="username" <?= $disabled ?>>
              <label for="userName">Benutzername</label>
            </div>
          </div>
          <div class="row">
            <div class="input-field col s12 l6 offset-l3">
              <input type="password" id="password" name="password" <?= $disabled ?>>
              <label for="password">Passwort</label>
            </div>
          </div>
          <div class="row">
            <div class="col s12 l6 offset-l3">
              <?php if( isset($version['username'])) { ?>
                Willkommen <?= $version['username'] ?>. Du bist angemeldet mit Rolle <?= $version['userrole'] ?>. 
                <a href="<?= $loginButtonHref ?>" class="btn red"><i class="material-icons right">logout</i>Abmelden</a>
              <?php } else { ?>
                <button id="loginBtn" class="btn center red <?= $disabled ?>">Anmelden</button>
              <?php } ?>
            </div>
          </div>                           
        </form>
        <div class="row">
          <div class="col s12 l6 offset-l3 red">
            <?php
              foreach($loginMessages as $message) {
                echo $message;
              }
            ?>
          </div>
        </div>
      </section>
      <div class="divider"></div>
      <?php if( $showCreateUserForm ) { ?>
        <section>
          <h4 class="center">Benutzer anlegen</h4>
          <p>Nur Super-Administratoren können neue Benutzer anlegen.</p>
          <form name="createUserForm" method="post" action="#">
            <div class="row">
              <div class="input-field col s12 l6 offset-l3">
                <input type="text" id="create_username" name="create_username">
                <label for="create_username">Benutzername</label>
              </div>
            </div>
            <div class="row">
              <div class="input-field col s12 l6 offset-l3">
                <input type="password" id="create_userpassword" name="create_password">
                <label for="create_userpassword">Passwort</label>
              </div>
            </div>
            <div class="row">
              <div class="input-field col s12 l6 offset-l3">
                <select id="create_userrole" name="create_role">
                  <option value="user">Benutzer</option>
                  <option value="admin">Administrator</option>
                  <option value="superuser">Super-User</option>
                </select>
                <label for="create_userrole">Rolle:</label>
              </div>
            </div>          
            <div class="row">
              <div class="col s12 l6 offset-l3">
                <button id="createUserBtn" class="btn center red">Anlegen</button>
              </div>
            </div>
          </form>
          <div class="row">
            <div class="col s12 l6 offset-l3 red">
              <?php
                foreach($createUserMessages as $message) {
                  echo $message;
                }
              ?>
            </div>
        </div>
        </section>
        <div class="divider"></div>
      <?php } ?>
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
  <script>
    // initialize the static select elements
      M.FormSelect.init(document.querySelectorAll('select'));
  </script>
</html>
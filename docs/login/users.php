<?php
include('db_users.php');
?>
<!doctype html>
<html lang="de">
  <head>
    <title>Benutzerverwaltung für den Planer für Leichtathletik-Trainings</title>
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
          <a href="#" class="brand-logo left"><img src="../assets/tsvlogo.png" alt="Logo" height="50" class="left">Benutzerverwaltung</a>
          <ul class="right">
            <li class="active"><a href="<?= $loginButtonHref ?>" id="loginBtn"
              class="btn-small red <?= $loginMenuItemDisabled ?>"><i class="material-icons"><?= $loggedIn ? 'logout' : 'login' ?></i></a></li>
          </ul>
        </div>
      </nav>
    </header>
    <main class="container">
      <section>
        <h4 class="center">Benutzer verwalten</h4>
        <p>Nur Administratoren können Benutzer verwalten.</p>
        <?php if( $loggedIn && $canManageUsers ) { ?>
          <div class="row">
            <div class="col s4"><!-- Benutzer zum Auswählen -->
              <ul class="collection with-header" id="exercise-list">
                <li class="collection-header"><h6>Wähle den Benutzer aus:</h6>
                  <form class="col s12" id="user-list-form" action="#">
                    <div class="input-field col s12">
                      <i class="material-icons prefix">search</i>
                      <input id="user-filter" type="text" name="filterBy" class="validate" value="<?= $filter ?>">
                    </div>
                  </form>
                </li>
                <?php
                  foreach($users as $user) { ?>
                  <li class="collection-item left-align">
                    <a class="red-text" href="?filterBy=<?= $filter ?>&selected=<?= urlencode($user->getName()) ?>"><?= $user->getName() ?></a>
                  </li>
                <?php
                  }
                ?>
              </ul>
            </div>
            <section class="container">
            <div class="col s8">
              <form name="manageUserForm" method="post" action="#">
                <div class="row">
                  <div class="input-field col s12">
                    <input type="text" id="username" name="username" value="<?= $username ?>">
                    <label for="username">Benutzername</label>
                  </div>
                </div>
                <div class="row">
                  <div class="input-field col s12">
                    <input type="password" id="password" name="password">
                    <label for="password">Passwort</label>
                  </div>
                </div>
                <div class="row">
                  <div class="input-field col s12">
                    <select id="userrole" name="role">
                      <option value="">-- Rolle auswählen --</option>
                      <option value="user" <?= $role==='user' ? 'selected' : '' ?>>Benutzer</option>
                      <option value="admin" <?= $role==='admin' ? 'selected' : '' ?>>Administrator</option>
                      <?php if($version['userrole']==='superuser') { ?>
                      <option value="superuser" <?= $role==='superuser' ? 'selected' : '' ?>>Super-User</option>
                      <?php } ?>
                    </select>
                    <label for="role">Rolle:</label>
                  </div>
                </div>          
                <div class="row">
                  <div class="col s12">
                    <button id="createUserBtn" name="action" value="create" class="btn center red">Anlegen
                      <i class="material-icons right">person_add</i>
                    </button>
                    <button id="updateUserBtn" name="action" value="update" class="btn center red">Ändern
                      <i class="material-icons right">save</i></button>
                    <button id="deleteUserBtn" name="action" value="delete" class="btn center red">Löschen
                      <i class="material-icons right">delete</i>
                    </button>
                  </div>
                </div>
              </form>
              <div class="row">
                <div class="col s12">
                  <?php
                    foreach($messages as $message) {
                      echo $message;
                    }
                  ?>
                </div>
              </div>
            </div>
          </section>
          </div>
      <?php } ?>
    </section>
    <div class="divider"></div>
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
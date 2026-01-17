# Test einer App zur Planung/Vorbreitung von Leichtathletik-Trainings

- Version auf Basis einer statischen Liste von Übungen: Aufruf der App über Link: https://marpeter.github.io/la-training-planner/
  - Mit dieser Version können Trainingspläne
    - nach Auswahl einer oder mehrerer Disziplinen und der gewünschten Trainingsdauer, auf Basis einer statischen Liste von Übungen, erzeugt werden.
    - aus einem Pool hinterlegter "Favoriten"-Pläne nach Auswahl der gewünschten Trainingsdauer geladen werden. Werden auch Disziplinen ausgewählt, werden nur Favoriten geladen, die für alle ausgewählten Disziplinen passen.
  - Die zur Verfügung stehenden Übungen und "Favoriten" sind jeweils in einer CSV Datei auf dem Web Server hinterlegt. Diese CSV Dateien können nicht durch die Benutzerinnen und Benutzer verändert werden. Entsprechend können auch keine erzeugten Pläne als Favoriten gespeichert werden.
- Version mit echtem PHP und MySQL/MariaDB Backend. Mit dieser Version können auch Übungen geändert, angelegt und gelöscht werden. Auch das Speichern generierter Pläne als Favoriten und das Ändern und Löschen von Favoriten wird unterstützt. Um diese Version zu nutzen, müssen Sie:
  - Die Inhalte des doc-Verzeichnisses in einen Ordner eines Web Server kopieren, auf dem auch PHP und MySQL/MariaDB installiert sind.
  - Die Zugriffsrechte auf diesen Web Server Ordner und die darin enthaltenen Unterordner und Datein so ändern, dass der Benutzer, unter dem der Web Server läuft, darin ein Verzeichnis `config` und darin eine Datei anlegen kann.
  - Die App im Web Browser aufrufen. Sie sollte automatisch auf eine Seite umleiten, mit deren Hilfe die Installation abgeschlossen werden kann. Auf dieser Seite geben Sie
    - Name und Passwort des Benutzers an, der als erster Benutzer der App angelegt werden soll. Dieser Benutzer kann nach erfolgreicher Installation weitere Benutzer anlegen.
    - Name und Passwort eines Datenbank-Benutzerkontos an, das die Rechte besitzt, weitere Datenbank-Benutzerkonten und Datenbanken anzulegen.
    - Und drücken Sie den Knopf "Installation abschließen'.
  - Sofern alles funktioniert werden Sie als der erste Benutzer angemeldet und auf die Seite weitergeleitet, auf der Sie die mitgelieferten Datenbank-Inhalte importieren können. Damit ist die Installation beendet.
  - Sollte die Installation so nicht abgeschlossen werden können, versuchen Sie, sie per Hand abzuschließen:
    - Falls die Datenbank nicht angelegt werden konnte, legen Sie sie über Datei data/db_setup.sql an (z.B. per Kommandozeile `mysql --user=... <admin/db_setup.sql`).
    - Falls kein Datenbank-Konto mit Lese- und Schreibrechten für die Datenbank angelegt werden konnte, legen Sie es an.
      Falls es bereits angelegt wurde, ändern Sie das Passwort.
    - Falls Tabelle `la_planner.users` keinen Eintrag mit `role = superuser` enthält, fügen Sie einen ein.
      Beachten Sie, dass das Feld `password` das Passwort nicht im Klartext enthält, sondern den per PHP-Funktion `password_hash` gehashten Wert.
    - Legen Sie ein Vereichnis `config` an.
    - Erstellen Sie in diesem Verzeichnis die Datei `config.php` mit folgendem Inhalt an:
    ```
    <?php
    
    $CONFIG = array(
      'dbhost' => 'localhost',
      'dbname' => 'la_planner', # Name der Datenbank 
      'dbuser' => '<NAME DES DATENBANK-KONTOS>',
      'dbpassword' => '<PASSWORT DES DATENBANK-KONTOS>'
    );
    ```
    - Laden Sie die Web-Anwendung erneut und melden Sie sich mit dem Benutzer, den Tabelle `la_planner.users` enthält, an. 
    - Die Tabelleninhalte können über die Hilfsfunktionen `https://.../import-export.html` gefüllt werden.
  
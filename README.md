# Test einer App zur Planung/Vorbreitung von Leichtathletik-Trainings

- Version mit statischen Trainingsplänen: https://marpeter.github.io/la-training-planner/
- Version, die die Trainingspläne nach Auswahl einer oder mehrerer Disziplinen erzeugt, auf Basis einer statischen Liste von Übungen: https://marpeter.github.io/la-training-planner/planner-v2.html.
  D.h. die zur Verfügung stehenden Übungen sind in einer CSV Datei auf dem Web Server hinterlegt, die nicht durch die Benutzerinnen und Benutzer verändert werden kann. Entsprechend können auch keine erzeugten Pläne als Favoriten gespeichert werden.
- Version mit echtem Backend, die auch Änderungen der Übungen (geplant) und das Speichern generierter Pläne als Favoriten unterstützt:
  - Die Inhalte des doc-Verzeichnisses in einen Ordner eines Web Server kopieren, auf dem auch PHP und MySQL/MariaDB installiert sind.
  - In MySQL/MariaDB eine Datenbank anlegen
  - Die Datenbank-Inhalte über Datei data/db_setup.sql füllen (z.B. per Kommandozeile `mysql --user=... <data/db_setup.sql`).
  - Der PHP Umgebung den Hostname, Datenbank-User und Passwort und den Namen der Datenbank über Umgebungsvariablen bekannt machen:
    - `LA_PLANNER_HOSTNAME`
    - `LA_PLANNER_USERNAME`
    - `LA_PLANNER_PASSWORD`
    - `LA_PLANNER_DBNAME`  
  
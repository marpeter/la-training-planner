# Test einer App zur Planung/Vorbreitung von Leichtathletik-Trainings

- Version auf Basis einer statischen Liste von Übungen: Aufruf der App über Link: https://marpeter.github.io/la-training-planner/
  - Mit dieser Version können Trainingspläne
    - nach Auswahl einer oder mehrerer Disziplinen und der gewünschten Trainingsdauer, auf Basis einer statischen Liste von Übungen, erzeugt werden.
    - aus einem Pool hinterlegter "Favoriten"-Pläne nach Auswahl der gewünschten Trainingsdauer geladen werden. Werden auch Disziplinen ausgewählt werden nur Favoriten geladen, die für alle ausgewählten Disziplinen passen.
  - Die zur Verfügung stehenden Übungen und "Favoriten" sind jeweils in einer CSV Datei auf dem Web Server hinterlegt. Diese CSV Dateien können nicht durch die Benutzerinnen und Benutzer verändert werden. Entsprechend können auch keine erzeugten Pläne als Favoriten gespeichert werden.
- Version mit echtem Backend, die auch Änderungen der Übungen (geplant) und das Speichern generierter Pläne als Favoriten (geplant) unterstützt:
  - Die Inhalte des doc-Verzeichnisses in einen Ordner eines Web Server kopieren, auf dem auch PHP und MySQL/MariaDB installiert sind.
  - In MySQL/MariaDB eine Datenbank anlegen
  - Die Datenbank-Inhalte über Datei data/db_setup.sql füllen (z.B. per Kommandozeile `mysql --user=... <data/db_setup.sql`).
  - Der PHP Umgebung den Hostname, Datenbank-User und Passwort und den Namen der Datenbank über Umgebungsvariablen bekannt machen:
    - `LA_PLANNER_HOSTNAME`
    - `LA_PLANNER_USERNAME`
    - `LA_PLANNER_PASSWORD`
    - `LA_PLANNER_DBNAME`  
  
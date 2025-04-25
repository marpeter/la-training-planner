CREATE SCHEMA IF NOT EXISTS la_planner CHARSET utf8;
CREATE TABLE IF NOT EXISTS la_planner.version (
    field VARCHAR(255) PRIMARY KEY,
    field_val TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
REPLACE INTO la_planner.version (field, field_val)
    VALUES ('number', '0.14.1'),
           ('date','2025-04-25'),
           ('supportsEditing', 'false'),
           ('supportsFavorites', 'false');

CREATE TABLE IF NOT EXISTS la_planner.disciplines (
    id CHAR(30) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

REPLACE INTO la_planner.disciplines (id, name, image) VALUES
  ('Ausdauer', 'Ausdauer', ''),
  ('Hochsprung', 'Hochsprung', 'assets/Hochsprung.png'),
  ('HochsprungOhneAnlage', 'Hochsprung ohne Anlage', 'assets/Hochsprung.png'),
  ('Koordination', 'Koordination', 'assets/Koordination.png'),
  ('Schnelligkeit', 'Schnelligkeit', 'assets/Lauf.png'),
  ('Schnelllaufen', 'Schnelllaufen', 'assets/Lauf.png'),
  ('Stabweitsprung', 'Stabweitsprung', 'assets/Weitsprung.png'),
  ('Staffellauf', 'Staffellauf', 'assets/Lauf.png'),
  ('Überlaufen', 'Überlaufen', 'assets/Huerdenlauf.png'),
  ('WeitsprungMitGrube', 'Weitsprung mit Grube', 'assets/Weitsprung.png'),
  ('WeitsprungOhneGrube', 'Weitsprung ohne Grube', 'assets/Weitsprung.png'),
  ('Wurf', 'Wurf', 'assets/Wurf.png');
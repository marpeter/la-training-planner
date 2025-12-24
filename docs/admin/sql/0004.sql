CREATE TABLE exercises (
    id CHAR(40) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    warmup BOOLEAN DEFAULT FALSE,
    runabc BOOLEAN DEFAULT FALSE,
    mainex BOOLEAN DEFAULT FALSE,
    ending BOOLEAN DEFAULT FALSE,
    material TEXT,
    durationmin TINYINT DEFAULT 05,
    durationmax TINYINT DEFAULT 15,
    repeats TEXT,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);

INSERT INTO exercises
  (id, name, warmup, runabc, mainex, ending, material, durationmin, durationmax, repeats, details) VALUES
  ('Auslaufen', 'Auslaufen', FALSE, FALSE, FALSE, FALSE, '', 5, 5, '2 Runden', '');

CREATE TABLE exercises_disciplines (
    exercise_id CHAR(40),
    discipline_id CHAR(30),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (exercise_id, discipline_id),
    FOREIGN KEY (exercise_id) REFERENCES exercises(id) ON DELETE CASCADE,
    FOREIGN KEY (discipline_id) REFERENCES disciplines(id) ON DELETE CASCADE);
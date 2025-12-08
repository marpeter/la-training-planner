CREATE SCHEMA IF NOT EXISTS la_planner CHARSET utf8;

DROP TABLE IF EXISTS la_planner.version;
CREATE TABLE la_planner.version (
    field VARCHAR(255) PRIMARY KEY,
    field_val TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO la_planner.version (field, field_val)
    VALUES ('number', '0.16.1'),
           ('date', '2025-12-04');

-- Drop dependent tables first
-- else foreign key relationships prevent dropping less dependent tables
DROP TABLE IF EXISTS la_planner.exercises_disciplines;
DROP TABLE IF EXISTS la_planner.favorite_disciplines;
DROP TABLE IF EXISTS la_planner.favorite_exercises;
DROP TABLE IF EXISTS la_planner.favorite_headers;

-------------------------------------------------
DROP TABLE IF EXISTS la_planner.disciplines;
CREATE TABLE la_planner.disciplines (
    id CHAR(30) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-------------------------------------------------
DROP TABLE IF EXISTS la_planner.exercises;
CREATE TABLE la_planner.exercises (
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

INSERT INTO la_planner.exercises
  (id, name, warmup, runabc, mainex, ending, material, durationmin, durationmax, repeats, details) VALUES
  ('Auslaufen', 'Auslaufen', FALSE, FALSE, FALSE, FALSE, '', 5, 5, '2 Runden', '');

-------------------------------------------------
CREATE TABLE la_planner.exercises_disciplines (
    exercise_id CHAR(40),
    discipline_id CHAR(30),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (exercise_id, discipline_id),
    FOREIGN KEY (exercise_id) REFERENCES la_planner.exercises(id) ON DELETE CASCADE,
    FOREIGN KEY (discipline_id) REFERENCES la_planner.disciplines(id) ON DELETE CASCADE);

-------------------------------------------------
CREATE TABLE la_planner.favorite_headers (
    id SMALLINT PRIMARY KEY,
    created_by VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    description VARCHAR(255));

CREATE TABLE la_planner.favorite_disciplines(
    favorite_id SMALLINT,
    discipline_id CHAR(30),
    PRIMARY KEY (favorite_id, discipline_id),
    FOREIGN KEY (favorite_id) REFERENCES la_planner.favorite_headers(id) ON DELETE CASCADE,
    FOREIGN KEY (discipline_id) REFERENCES la_planner.disciplines(id) ON DELETE CASCADE);

CREATE TABLE la_planner.favorite_exercises (
    favorite_id SMALLINT,
    phase CHAR(6),
    position TINYINT,
    exercise_id CHAR(40) NOT NULL,
    duration TINYINT NOT NULL,
    PRIMARY KEY (favorite_id, phase, position),
    FOREIGN KEY (favorite_id) REFERENCES la_planner.favorite_headers(id) ON DELETE CASCADE,
    FOREIGN KEY (exercise_id) REFERENCES la_planner.exercises(id) ON DELETE CASCADE);
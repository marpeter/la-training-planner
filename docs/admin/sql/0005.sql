CREATE TABLE favorite_headers (
    id SMALLINT PRIMARY KEY,
    created_by VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    description VARCHAR(255));

CREATE TABLE favorite_disciplines(
    favorite_id SMALLINT,
    discipline_id CHAR(30),
    PRIMARY KEY (favorite_id, discipline_id),
    FOREIGN KEY (favorite_id) REFERENCES favorite_headers(id) ON DELETE CASCADE,
    FOREIGN KEY (discipline_id) REFERENCES disciplines(id) ON DELETE CASCADE);

CREATE TABLE favorite_exercises (
    favorite_id SMALLINT,
    phase CHAR(6),
    position TINYINT,
    exercise_id CHAR(40) NOT NULL,
    duration TINYINT NOT NULL,
    PRIMARY KEY (favorite_id, phase, position),
    FOREIGN KEY (favorite_id) REFERENCES favorite_headers(id) ON DELETE CASCADE,
    FOREIGN KEY (exercise_id) REFERENCES exercises(id) ON DELETE CASCADE);
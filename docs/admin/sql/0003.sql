-- Drop dependent tables first
-- else foreign key relationships prevent dropping less dependent tables
DROP TABLE IF EXISTS exercises_disciplines;
DROP TABLE IF EXISTS favorite_disciplines;

DROP TABLE IF EXISTS disciplines;
CREATE TABLE disciplines (
    id CHAR(30) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
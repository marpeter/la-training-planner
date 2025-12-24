CREATE TABLE la_planner.disciplines (
    id CHAR(30) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
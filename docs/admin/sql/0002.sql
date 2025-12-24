CREATE TABLE la_planner.users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    `password` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    role VARCHAR(100) NOT NULL DEFAULT 'user',
    UNIQUE KEY username ( `username` )
);

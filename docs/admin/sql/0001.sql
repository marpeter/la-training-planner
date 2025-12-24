CREATE TABLE version (
    field VARCHAR(255) PRIMARY KEY,
    field_val TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO version (field, field_val)
    VALUES ('number', '0.17.1'),
           ('date', '2025-12-24');
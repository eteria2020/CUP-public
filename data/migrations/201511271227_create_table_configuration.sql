/**
 * Create table configurations
 */
CREATE TABLE configurations (
    id serial PRIMARY KEY,
    slug text NOT NULL,
    config_key text NOT NULL,
    config_value text NOT NULL
);

ALTER TABLE configurations OWNER TO sharengo;

/**
 * Populate the table with existing data
 */
INSERT INTO configurations VALUES (1, 'alarm', 'battery', '20');
INSERT INTO configurations VALUES (2, 'alarm', 'delay', '31');
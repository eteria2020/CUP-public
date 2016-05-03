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


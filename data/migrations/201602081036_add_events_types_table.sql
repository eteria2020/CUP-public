/**
 * Create the events_types table
 */
CREATE SEQUENCE events_types_id_seq INCREMENT BY 1 MINVALUE 1 START 1;
CREATE TABLE events_types (id INT NOT NULL, label VARCHAR(255) NOT NULL, map_logic VARCHAR(255) NOT NULL, description TEXT NOT NULL, notes TEXT DEFAULT NULL, PRIMARY KEY(id));



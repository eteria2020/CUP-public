ALTER TABLE cars_maintenance ADD COLUMN location_id INTEGER DEFAULT NULL;

ALTER TABLE ONLY cars_maintenance
    ADD CONSTRAINT fk_cras_maintenance_location FOREIGN KEY (location_id) REFERENCES maintenance_locations(id);
/*
 * make sure that 1 is in fact the id of the Milano fleet
 */
ALTER TABLE extra_payments ADD fleet_id INT NOT NULL DEFAULT 1 REFERENCES fleets(id);

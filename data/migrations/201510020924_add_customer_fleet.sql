/*
 * assicurarsi che 1 sia il fleet_id di Milano
 */
ALTER TABLE customers ADD fleet_id INT NOT NULL DEFAULT 1 REFERENCES fleets(id);

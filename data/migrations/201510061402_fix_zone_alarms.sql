/**
 * Add column to properly reference fleets
 */
ALTER TABLE zone_alarms ADD fleet_id integer REFERENCES fleets (id);

/**
 * Check whether the id's used here correspond to the ones in production
 */
UPDATE zone_alarms SET fleet_id = 1 WHERE name = 'MI';
UPDATE zone_alarms SET fleet_id = 2 WHERE name = 'FI';
ALTER TABLE zone_alarms ALTER COLUMN fleet_id SET NOT NULL;

/**
 * Remove the obsolete column
 */
ALTER TABLE zone_alarms DROP COLUMN name;

/**
 * Add active column to specify if zone should be considered or not
 */
ALTER TABLE zone_alarms ADD active boolean DEFAULT true NOT NULL;

/**
 * Remove the column that references the fleet
 */
ALTER TABLE zone_alarms DROP COLUMN name;

/**
 * Add active column to specify if zone should be considered or not
 */
ALTER TABLE zone_alarms ADD active boolean DEFAULT true NOT NULL;

/**
 * Create table for many-to-many relationship
 */
CREATE TABLE zone_alarms_fleets (
    zone_alarm_id int REFERENCES zone_alarms (id) ON UPDATE CASCADE,
    fleet_id int REFERENCES fleets (id) ON UPDATE CASCADE,
    CONSTRAINT "zone_alarms_fleets_pkey" PRIMARY KEY (zone_alarm_id, fleet_id)
);



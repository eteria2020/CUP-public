ALTER TABLE fleets ADD longitude NUMERIC DEFAULT NULL;
ALTER TABLE fleets ADD latitude NUMERIC DEFAULT NULL;
ALTER TABLE fleets ADD zoom_level INT DEFAULT NULL;
ALTER TABLE fleets ADD is_default BOOLEAN DEFAULT NULL;

UPDATE fleets SET latitude = 45.4627338, longitude = 9.1777323, zoom_level = 13, is_default = true WHERE id = 1;
UPDATE fleets SET latitude = 43.7794624, longitude = 11.2414829, zoom_level = 13, is_default = false WHERE id = 2;

ALTER TABLE fleets ALTER zoom_level SET NOT NULL;
ALTER TABLE fleets ALTER longitude SET NOT NULL;
ALTER TABLE fleets ALTER latitude SET NOT NULL;
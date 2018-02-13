ALTER TABLE pois ADD COLUMN unplug_enable BOOLEAN NOT NULL DEFAULT FALSE;
COMMENT ON COLUMN pois.unplug_enable IS 'Abilitazione al distacco cordset da cliente';

ALTER TABLE cars_info ADD COLUMN unplug_enable BOOLEAN  NOT NULL DEFAULT FALSE;
COMMENT ON COLUMN cars_info.unplug_enable IS 'Abilitazione al distacco cordset da cliente';

INSERT INTO free_fares (id, conditions, description, active) VALUES (11, '{"unplug_enable": {"value": 4}}', 'Distacco cordset da cliente', true);

INSERT INTO configurations (id, slug, config_key, config_value) VALUES (3, 'alarm', 'unplug_enable', '80');


CREATE OR REPLACE FUNCTION public.f_unplug_car_cordset()
RETURNS trigger AS $$
	DECLARE unplug_enable_new BOOLEAN := FALSE;
		unplug_enable_old BOOLEAN := FALSE;
		config_soc_min INTEGER := 80;
		pois_distance FLOAT := 99999.9;
		POIS_DISTANCE_MAX CONSTANT FLOAT := 100.0;

BEGIN

	unplug_enable_old := (SELECT unplug_enable FROM cars_info WHERE car_plate=NEW.plate);

	IF (NEW.plug AND NEW.charging) THEN
		config_soc_min := (SELECT CAST (MAX (config_value) AS INT) FROM configurations WHERE config_key='unplug_enable');
		
		IF (NEW.soc>=config_soc_min) THEN
			pois_distance = (SELECT MIN(COALESCE(ST_Distance_Sphere(ST_MakePoint(c.longitude, c.latitude) , ST_MakePoint(p.lon, p.lat)), 9999999)) dist
				FROM cars c
				CROSS JOIN pois p 
				WHERE p.unplug_enable=TRUE AND c.plate = NEW.plate);
			
			IF pois_distance<=POIS_DISTANCE_MAX THEN
				unplug_enable_new = TRUE;
			END IF;
		END IF;
	END IF;

	IF (unplug_enable_new <> unplug_enable_old) THEN
		UPDATE cars_info SET unplug_enable=unplug_enable_new WHERE car_plate=NEW.plate;
	END IF;
	
   RETURN NEW;
END;

$$ LANGUAGE 'plpgsql';

CREATE TRIGGER unplug_car_cordset AFTER UPDATE ON cars FOR EACH ROW EXECUTE PROCEDURE f_unplug_car_cordset();
CREATE OR REPLACE FUNCTION public.f_unplug_car_cordset() RETURNS trigger AS $$

BEGIN

	DECLARE
		pois_distance FLOAT := 99999.9;
		POIS_DISTANCE_MAX CONSTANT FLOAT := 100.0;
		unplug_old BOOLEAN := FALSE;
		unplug_new BOOLEAN := FALSE;

	BEGIN
		unplug_old = (SELECT unplug_enable FROM cars_bonus WHERE car_plate=NEW.plate);
		unplug_new = unplug_old;

		IF (NEW.charging = TRUE) THEN
			pois_distance = (
				SELECT MIN(COALESCE(ST_Distance_Sphere(ST_MakePoint(NEW.longitude, NEW.latitude) , ST_MakePoint(p.lon, p.lat)), pois_distance)) dist
				FROM  pois p 
				WHERE p.unplug_enable=TRUE);

			IF (pois_distance<=POIS_DISTANCE_MAX) THEN
				unplug_new = TRUE;
			ELSE
				unplug_new = FALSE;
			END IF;	
		ELSE			
			unplug_new = FALSE;
		END IF;
		
		IF (unplug_old != unplug_new) THEN
			UPDATE cars_bonus SET unplug_enable = unplug_new WHERE car_plate=NEW.plate;
		END IF;
		
		RETURN NEW;
	END;

END

$$ LANGUAGE plpgsql;
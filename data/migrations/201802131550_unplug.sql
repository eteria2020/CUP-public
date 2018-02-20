ALTER TABLE pois ADD COLUMN unplug_enable BOOLEAN NOT NULL DEFAULT FALSE;
COMMENT ON COLUMN pois.unplug_enable IS 'Abilitazione al distacco cordset da cliente';

ALTER TABLE cars_bonus ADD COLUMN unplug_enable BOOLEAN  NOT NULL DEFAULT FALSE;
COMMENT ON COLUMN cars_bonus.unplug_enable IS 'Abilitazione al distacco cordset da cliente';

INSERT INTO free_fares (id, conditions, description, active) VALUES (11, '{"unplug_enable": {"value": 4}}', 'Distacco cordset da cliente', true);

INSERT INTO configurations (id, slug, config_key, config_value) VALUES (3, 'alarm', 'unplug_enable', '80');


CREATE OR REPLACE FUNCTION public.f_unplug_car_cordset()
RETURNS trigger AS $$

DECLARE
    pois_distance FLOAT := 99999.9;
    POIS_DISTANCE_MAX CONSTANT FLOAT := 100.0;

BEGIN
    IF (OLD.plug = FALSE AND NEW.plug = TRUE) THEN
        pois_distance = (SELECT MIN(COALESCE(ST_Distance_Sphere(ST_MakePoint(NEW.longitude, NEW.latitude) , ST_MakePoint(p.lon, p.lat)), 9999999)) dist
        FROM  pois p 
        WHERE p.unplug_enable=TRUE);

        IF pois_distance<=POIS_DISTANCE_MAX THEN
            UPDATE cars_bonus SET unplug_enable=TRUE WHERE car_plate=NEW.plate;
        END IF; 

    ELSIF (OLD.charging = TRUE AND NEW.charging = FALSE) THEN 
        UPDATE cars_bonus SET unplug_enable=FALSE WHERE car_plate=NEW.plate;
    END IF;
    RETURN NEW;
END;

$$ LANGUAGE 'plpgsql';

CREATE TRIGGER unplug_car_cordset AFTER UPDATE ON cars FOR EACH ROW EXECUTE PROCEDURE f_unplug_car_cordset();
/*
* create table
*/
CREATE TABLE cars_bonus(car_plate text PRIMARY KEY, nouse timestamp with time zone default NULL);

/*
* functions
*/

CREATE OR REPLACE FUNCTION public.f_cars_bonus()
RETURNS trigger AS
$BODY$
BEGIN
	IF (NEW.timestamp_end IS NOT NULL AND (OLD.timestamp_end IS NULL OR NEW.timestamp_end > OLD.timestamp_end)) THEN
		UPDATE cars_bonus SET nouse=NEW.timestamp_end WHERE car_plate = NEW.car_plate AND nouse < NEW.timestamp_end;
	END IF;
   RETURN NEW;
END;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION public.f_cars_bonus() OWNER TO sharengo;

CREATE TRIGGER trigger_cars_bonus_update BEFORE UPDATE ON trips FOR EACH ROW EXECUTE PROCEDURE public.f_cars_bonus();

CREATE OR REPLACE FUNCTION public.f_create_cars_bonus()
  RETURNS trigger AS
$BODY$
BEGIN
	INSERT INTO cars_bonus (car_plate) VALUES (NEW.plate);
	RETURN NEW;
END;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;

ALTER FUNCTION public.f_create_cars_bonus() OWNER TO sharengo;

CREATE TRIGGER create_car_bonus AFTER INSERT ON cars FOR EACH ROW EXECUTE PROCEDURE f_create_cars_bonus();

CREATE OR REPLACE FUNCTION public.f_delete_cars_bonus()
  RETURNS trigger AS
$BODY$
BEGIN
	DELETE FROM cars_bonus WHERE car_plate = OLD.plate;
	RETURN OLD;
END;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;

ALTER FUNCTION public.f_delete_cars_bonus() OWNER TO sharengo;

CREATE TRIGGER delete_car_bonus BEFORE DELETE ON cars FOR EACH ROW EXECUTE PROCEDURE f_delete_cars_bonus();
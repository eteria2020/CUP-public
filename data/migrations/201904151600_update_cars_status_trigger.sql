CREATE OR REPLACE FUNCTION "public"."notifycarsstatus" () RETURNS trigger AS 'DECLARE
BEGIN
	IF (NEW.status != OLD.status) THEN
		PERFORM pg_notify(CAST(''cars_status'' AS text),CAST(NEW.plate AS text)|| '','' || CAST((SELECT imei from cars_telemetry where car_plate = NEW.plate) AS TEXT) || '','' ||  CAST(NEW.status AS text));
	END IF;

	RETURN NEW;

END;' LANGUAGE "plpgsql" COST 100
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER;


CREATE TRIGGER trigger_cars_status AFTER UPDATE ON cars FOR EACH ROW WHEN (NEW.status != OLD.status) EXECUTE PROCEDURE notifycarsstatus();
CREATE OR REPLACE FUNCTION "public"."notifynewreservation" () RETURNS trigger AS 'DECLARE
BEGIN
  IF (NEW.to_send) THEN
	PERFORM pg_notify(CAST(''reservations'' AS text),CAST(NEW.id AS text)|| '','' || CAST(NEW.car_plate AS text) || '',true''|| '','' || CAST((SELECT software_version from cars where plate = new.car_plate) AS text));
  END IF;
  RETURN NEW;
END;
' LANGUAGE "plpgsql" COST 100
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER;

CREATE OR REPLACE FUNCTION "public"."notifynewcommand" () RETURNS trigger AS 'DECLARE
BEGIN
  IF (NEW.to_send) THEN
	PERFORM pg_notify(CAST(''commands'' AS text),CAST(NEW.id AS text)|| '','' || CAST(NEW.car_plate AS text) || '',true''|| '','' || CAST((SELECT software_version from cars where plate = new.car_plate) AS text));
  END IF;
  RETURN NEW;
END;
' LANGUAGE "plpgsql" COST 100
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER;


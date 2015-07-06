CREATE OR REPLACE FUNCTION public.notifycommand()
  RETURNS trigger AS
$BODY$
DECLARE
BEGIN
  IF (NEW.to_send <> OLD.to_send) THEN
	PERFORM pg_notify(CAST('commands' AS text),CAST(NEW.id AS text)|| ','
|| CAST(NEW.car_plate AS text)|| ',' || CAST(NEW.to_send AS TEXT));
  END IF;
  RETURN NEW;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION public.notifycommand()
  OWNER TO sharengo;


CREATE TRIGGER notifycommand
  AFTER UPDATE
  ON public.commands
  FOR EACH ROW
  EXECUTE PROCEDURE public.notifycommand();

CREATE OR REPLACE FUNCTION public.notifynewcommand()
  RETURNS trigger AS
$BODY$
DECLARE
BEGIN
  IF (NEW.to_send) THEN
	PERFORM pg_notify(CAST('commands' AS text),CAST(NEW.id AS text)|| ','
|| CAST(NEW.car_plate AS text) || ',true');
  END IF;
  RETURN NEW;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION public.notifynewcommand()
  OWNER TO sharengo;

CREATE TRIGGER notifynewcommand
  AFTER INSERT
  ON public.commands
  FOR EACH ROW
  EXECUTE PROCEDURE public.notifynewcommand();

CREATE OR REPLACE FUNCTION notifyreservation()
  RETURNS trigger AS
$BODY$
DECLARE
BEGIN
  IF (NEW.to_send <> OLD.to_send) THEN
	PERFORM pg_notify(CAST('reservations' AS text),CAST(NEW.id AS text)||
',' || CAST(NEW.car_plate AS text)|| ',' || CAST(NEW.to_send AS TEXT));
  END IF;
  RETURN NEW;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION notifyreservation()
  OWNER TO sharengo;


CREATE OR REPLACE FUNCTION notifynewreservation()
  RETURNS trigger AS
$BODY$
DECLARE
BEGIN
  IF (NEW.to_send) THEN
	PERFORM pg_notify(CAST('reservations' AS text),CAST(NEW.id AS text)||
',' || CAST(NEW.car_plate AS text) || ',true');
  END IF;
  RETURN NEW;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION notifynewreservation()
  OWNER TO sharengo;


CREATE TRIGGER notifyreservation
  AFTER UPDATE
  ON reservations
  FOR EACH ROW
  EXECUTE PROCEDURE notifyreservation();


CREATE TRIGGER notifynewreservation
  AFTER INSERT
  ON reservations
  FOR EACH ROW
  EXECUTE PROCEDURE notifynewreservation();
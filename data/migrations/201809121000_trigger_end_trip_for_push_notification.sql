CREATE OR REPLACE FUNCTION public.notifytripclose()
    RETURNS trigger AS
$BODY$
BEGIN
    IF (NEW.timestamp_end IS NOT NULL AND OLD.timestamp_end IS NULL) THEN
        PERFORM pg_notify(CAST('trip' AS text),CAST(NEW.id AS text)|| ',' || CAST(NEW.customer_id AS text)|| ',' || CAST((SELECT email from customers where id = NEW.customer_id) AS TEXT)|| ',' || CAST(NEW.timestamp_beginning AS text)|| ',' || CAST(NEW.timestamp_end - NEW.timestamp_beginning AS text));
    END IF;
    RETURN NEW;
END;

$BODY$
LANGUAGE plpgsql VOLATILE
COST 100;

CREATE TRIGGER trigger_trip_end AFTER UPDATE ON trips FOR EACH ROW WHEN (((new.timestamp_end IS NOT NULL) AND (old.timestamp_end IS NULL))) EXECUTE PROCEDURE notifytripclose()
CREATE OR REPLACE FUNCTION "public"."notifytripclose" () RETURNS trigger AS 'DECLARE
BEGIN
	IF (NEW.timestamp_end IS NOT NULL AND OLD.timestamp_end IS NULL) THEN
		PERFORM pg_notify(CAST(''trip'' AS text),CAST(NEW.id AS text)|| '','' || CAST(NEW.customer_id AS text)|| '','' || CAST((SELECT email from customers where id = NEW.customer_id) AS TEXT)|| '','' || CAST(NEW.timestamp_beginning AS text)|| '','' || CAST(NEW.timestamp_end - NEW.timestamp_beginning AS text)|| '','' || CAST(NEW.car_plate AS text));
	END IF;

	RETURN NEW;

END;' LANGUAGE "plpgsql" COST 100
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER;
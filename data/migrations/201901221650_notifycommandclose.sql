CREATE OR REPLACE FUNCTION "public"."notifycommandclose" () RETURNS trigger AS 'DECLARE
BEGIN

PERFORM pg_notify(CAST(''command_close'' AS text),CAST(NEW.id AS text)|| '','' || CAST(NEW.car_plate AS text)|| '','' || CAST(NEW.queued AS TEXT) || '','' || CAST(NEW.txtarg1 AS TEXT));
  RETURN NEW;
END;'
LANGUAGE "plpgsql" COST 100
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER;

CREATE OR REPLACE FUNCTION "public"."notifycommandclosereceived" () RETURNS trigger AS 'DECLARE
BEGIN

PERFORM pg_notify(CAST(''command_close_received'' AS text),CAST(NEW.id AS text)|| '','' || CAST(NEW.car_plate AS text)|| '','' || CAST(NEW.received AS TEXT)|| '','' || CAST(NEW.txtarg1 AS TEXT));

  RETURN NEW;
END;' LANGUAGE "plpgsql" COST 100
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER;

CREATE TRIGGER trigger_notify_command_close AFTER INSERT ON public.commands FOR EACH ROW WHEN ((((new.webuser_id IS NULL) AND (new.command = 'CLOSE_TRIP'::text)) AND (new.txtarg1 <> ''::text))) EXECUTE PROCEDURE notifycommandclose();

CREATE TRIGGER trigger_notify_command_close_received AFTER UPDATE ON public.commands FOR EACH ROW WHEN ((((((new.webuser_id IS NULL) AND (new.command = 'CLOSE_TRIP'::text)) AND (new.txtarg1 <> ''::text)) AND (old.to_send = true)) AND (new.to_send = false))) EXECUTE PROCEDURE notifycommandclosereceived();

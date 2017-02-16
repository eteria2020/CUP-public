CREATE OR REPLACE FUNCTION notifynewmessage()
RETURNS trigger AS
$BODY$
DECLARE
BEGIN
  PERFORM pg_notify(CAST('message_outbox' AS text),CAST(NEW.id AS text)|| ',true');
  RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql VOLATILE
COST 100;

ALTER FUNCTION notifynewmessage()
  OWNER TO sharengo;

CREATE TRIGGER notifynewmessage
  AFTER INSERT
  ON public.messages_outbox
  FOR EACH ROW
  EXECUTE PROCEDURE public.notifynewmessage();
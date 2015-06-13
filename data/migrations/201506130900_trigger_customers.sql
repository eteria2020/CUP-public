
CREATE SEQUENCE public.customers_update_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 13
  CACHE 1;
ALTER TABLE public.customers_update_seq
  OWNER TO sharengo;



CREATE OR REPLACE FUNCTION public.f_customers_update_sequence()
  RETURNS trigger AS
$BODY$
BEGIN
   NEW.update_id= nextval('customers_update_seq'::regclass);
   NEW.update_ts= floor(date_part('epoch',now()));
   RETURN NEW;
END;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION public.f_customers_update_sequence()
  OWNER TO sharengo;


CREATE TRIGGER trigger_customers_update BEFORE INSERT OR UPDATE
   ON customers FOR EACH ROW
   EXECUTE PROCEDURE public.f_customers_update_sequence();
CREATE TABLE pois
(
  id  serial,
  type text NOT NULL,
  code text,
  name text,
  brand text,
  address text,
  town text,
  zip_code text,
  province text,
  lon numeric,
  lat numeric,
  update bigserial,
  CONSTRAINT pois_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);

ALTER TABLE pois
  OWNER TO sharengo;


CREATE OR REPLACE FUNCTION public.f_pois_update_sequence()
  RETURNS trigger AS
$BODY$
BEGIN
   NEW.update = nextval('pois_update_seq'::regclass);
   RETURN NEW;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION public.f_pois_update_sequence()
  OWNER TO sharengo;


CREATE TRIGGER trigger_pois_update
  BEFORE UPDATE
  ON public.pois
  FOR EACH ROW
  EXECUTE PROCEDURE public.f_pois_update_sequence();

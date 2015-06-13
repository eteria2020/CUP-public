CREATE TABLE events
(
  id  bigserial,
  event_time timestamp with time zone,
  server_time timestamp with time zone,
  car_plate text,
  event_id integer,
  label text,
  level integer,
  customer_id integer,
  trip_id integer,
  txtval text,
  intval integer,
  geo geometry,
  lon numeric,
  lat numeric,
  km integer,
  battery integer,
  mac text,
  imei text,
  data jsonb,
  CONSTRAINT pk_events_id PRIMARY KEY (id)
  )
WITH (
  OIDS=FALSE
);
ALTER TABLE events
  OWNER TO sharengo;


-- Index: public.idx_eventi_id_evento

-- DROP INDEX public.idx_eventi_id_evento;

CREATE INDEX idx_events_event_id
  ON events
  USING btree
  (event_id);

-- Index: public.idx_eventi_id_veicolo

-- DROP INDEX public.idx_eventi_id_veicolo;

CREATE INDEX idx_events_car_plate
  ON events
  USING btree
  (car_plate COLLATE pg_catalog."default");

-- Index: public.idx_eventi_timestamp

-- DROP INDEX public.idx_eventi_timestamp;

CREATE INDEX idx_events_event_time
  ON events
  USING btree
  (event_time);

CREATE TABLE logs
(
  log_time timestamp with time zone NOT NULL DEFAULT now(),
  car_plate text NOT NULL,
  km integer,
  battery integer,
  speed integer,
  key_status boolean,
  lon double precision,
  lat double precision,
  geo geometry,
  on_charge boolean,
  on_trip boolean,
  id_trip integer,
  cputemp integer,
  uptime integer,
  imei text,
  gps_info jsonb,
  detail jsonb,
  CONSTRAINT pk_log PRIMARY KEY (log_time, car_plate)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE logs
  OWNER TO sharengo;
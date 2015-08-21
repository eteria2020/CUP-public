ALTER TABLE cars ADD COLUMN gps_data jsonb;
ALTER TABLE cars ADD COLUMN park_enabled boolean NOT NULL DEFAULT false;

CREATE TABLE public.gps_alarms
(
   alarm_time timestamp with time zone,
   car_plate text,
   lon numeric,
   lat numeric,
   gps_data jsonb,
   CONSTRAINT pk_gps_alarms PRIMARY KEY (alarm_time, car_plate)
)
WITH (
  OIDS = FALSE
);
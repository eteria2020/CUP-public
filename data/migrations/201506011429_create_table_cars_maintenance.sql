CREATE TABLE cars_maintenance (
    id SERIAL PRIMARY KEY,
    car_plate text NOT NULL,
    webuser_id integer NOT NULL,
    location text NOT NULL,
    notes text,
    update_ts timestamp without time zone NOT NULL
);

ALTER TABLE public.cars_maintenance OWNER TO sharengo;
ALTER TABLE cars_maintenance ADD CONSTRAINT webuser_fk FOREIGN KEY (webuser_id) REFERENCES webuser (id);

CREATE TABLE update_cars (
    id SERIAL PRIMARY KEY,
    car_plate text NOT NULL,
    webuser_id integer NOT NULL,
    location text NOT NULL,
    status car_status NOT NULL,
    note text,
    update timestamp without time zone NOT NULL
);

ALTER TABLE public.update_cars OWNER TO sharengo;

ALTER TABLE update_cars ADD CONSTRAINT car_fk FOREIGN KEY (car_plate) REFERENCES cars (plate);
ALTER TABLE update_cars ADD CONSTRAINT webuser_fk FOREIGN KEY (webuser_id) REFERENCES webuser (id);
CREATE TABLE reservations (
    id SERIAL PRIMARY KEY,
    ts timestamp with time zone NOT NULL,
    car_plate text NOT NULL,
    customer_id integer NOT NULL,
    beginning_ts timestamp with time zone NOT NULL,
    active boolean NOT NULL DEFAULT true,
    cards jsonb, -- text if psql 9.1,
    length int NOT NULL,
    to_send boolean DEFAULT true,
    sent_ts timestamp with time zone NOT NULL,
    consumed_ts timestamp with time zone 
);

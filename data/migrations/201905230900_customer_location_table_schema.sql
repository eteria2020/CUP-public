CREATE TABLE customer_locations (
    id integer NOT NULL,
    customer_id integer NOT NULL,
    latitude numeric NOT NULL,
    longitude numeric NOT NULL,
    action text NOT NULL,
    "timestamp" timestamp with time zone NOT NULL,
    car_plate text,
    ip text,
    port text,
    calling_app text,
    user_agent text
);


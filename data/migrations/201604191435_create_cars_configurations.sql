CREATE TABLE cars_configurations (
    id integer NOT NULL,
    fleet_id integer,
    model text,
    car_plate text,
    key text NOT NULL,
    value text NOT NULL
);

CREATE SEQUENCE car_configs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE ONLY cars_configurations ALTER COLUMN id SET DEFAULT nextval('car_configs_id_seq'::regclass);

ALTER TABLE cars_bonus ADD COLUMN free_x INTEGER DEFAULT NULL;





CREATE TABLE cars_bonus_history (
    id integer NOT NULL,
    inserted_ts timestamp(0) without time zone NOT NULL,
    plate text NOT NULL,
	free_x integer,
	permanance boolean NOT NULL
);

ALTER TABLE cars_bonus_history OWNER TO sharengo;


CREATE SEQUENCE cars_bonus_history_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE cars_bonus_history_id_seq OWNER TO sharengo;


ALTER SEQUENCE cars_bonus_history_id_seq OWNED BY cars_bonus_history.id;

ALTER TABLE ONLY cars_bonus_history ALTER COLUMN id SET DEFAULT nextval('cars_bonus_history_id_seq'::regclass);

ALTER TABLE ONLY cars_bonus_history
    ADD CONSTRAINT cars_bonus_history_pkey PRIMARY KEY (id);
	
ALTER TABLE ONLY cars_bonus_history
    ADD CONSTRAINT fk_cars_bonus_history_plate FOREIGN KEY (plate) REFERENCES cars(plate);
	

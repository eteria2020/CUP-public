CREATE SEQUENCE discount_state_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER SEQUENCE discount_state_id_seq OWNER TO sharengo;

CREATE TABLE discount_state (
    id INT DEFAULT nextval('discount_state_id_seq'::regclass) NOT NULL,
    customer_id INT REFERENCES customers(id),
    discount_state VARCHAR(255),
    PRIMARY KEY(id)
);

ALTER TABLE discount_state OWNER TO sharengo;
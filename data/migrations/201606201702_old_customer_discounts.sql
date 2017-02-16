CREATE SEQUENCE old_customer_discounts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE old_customer_discounts (
    id INT DEFAULT nextval('old_customer_discounts_id_seq'::regclass) NOT NULL,
    customer_id INT REFERENCES customers(id),
    discount INT,
    obsolete_from TIMESTAMP(0) WITHOUT TIME ZONE,
    PRIMARY KEY(id)
);

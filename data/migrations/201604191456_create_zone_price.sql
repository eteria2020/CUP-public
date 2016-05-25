CREATE SEQUENCE zone_price_id_seq
    START WITH 2
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE zone_price (
    id integer DEFAULT nextval('zone_price_id_seq'::regclass) NOT NULL,
    id_group_open integer,
    id_group_close integer,
    cost numeric,
    bonus numeric,
    note text
);

ALTER TABLE ONLY zone_price
    ADD CONSTRAINT pk_zone_price PRIMARY KEY (id);

ALTER TABLE ONLY zone_price
    ADD CONSTRAINT unique_zone_price UNIQUE (id_group_open, id_group_close);



CREATE SEQUENCE zone_id_seq
    START WITH 17
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE zone (
    id integer DEFAULT nextval('zone_id_seq'::regclass) NOT NULL,
    name text,
    area_invoice geometry,
    id_parent integer,
    active boolean,
    hidden boolean,
    invoice_description text,
    rev_geo boolean DEFAULT false NOT NULL,
    area_use geometry
);



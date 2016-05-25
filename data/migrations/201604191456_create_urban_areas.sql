
CREATE TABLE urban_areas (
    id integer NOT NULL,
    id_fleet integer NOT NULL,
    area geometry(Polygon,4326),
    id_area numeric(6,0),
    name character varying(100),
    area_ha numeric(18,5),
    area_mq numeric(18,5)
);

CREATE SEQUENCE urban_areas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER SEQUENCE urban_areas_id_seq OWNED BY urban_areas.id;

ALTER TABLE ONLY urban_areas ALTER COLUMN id SET DEFAULT nextval('urban_areas_id_seq'::regclass);

ALTER TABLE ONLY urban_areas
    ADD CONSTRAINT urban_areas_pkey PRIMARY KEY (id);

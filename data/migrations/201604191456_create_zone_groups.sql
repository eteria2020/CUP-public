CREATE SEQUENCE zone_groups_id_seq
    START WITH 13
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE zone_groups (
    id integer DEFAULT nextval('zone_groups_id_seq'::regclass) NOT NULL,
    description text,
    fleet_id integer,
    company_id integer,
    close_trip boolean DEFAULT true NOT NULL,
    id_zone integer[]
);


ALTER TABLE ONLY zone_groups
    ADD CONSTRAINT zone_groups_pk PRIMARY KEY (id);

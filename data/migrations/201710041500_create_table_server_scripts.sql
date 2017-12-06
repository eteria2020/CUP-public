CREATE TABLE server_scripts (
    id integer NOT NULL,
    start_ts timestamp(0) with time zone DEFAULT NULL::timestamp with time zone NOT NULL,
    end_ts timestamp(0) with time zone DEFAULT NULL::timestamp with time zone,
    name        text,
    full_path   text,
    param       jsonb,
    error       text,
    info_script jsonb,
    note        text
);

CREATE SEQUENCE serverscripts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE serverscripts_id_seq OWNER TO sharengo;

ALTER SEQUENCE serverscripts_id_seq OWNED BY server_scripts.id;

ALTER TABLE ONLY server_scripts ALTER COLUMN id SET DEFAULT nextval('serverscripts_id_seq'::regclass);

ALTER TABLE ONLY server_scripts ADD CONSTRAINT serverscripts_pkey PRIMARY KEY (id);
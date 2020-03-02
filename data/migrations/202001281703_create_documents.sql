CREATE SEQUENCE documents_id_seq START 1;
CREATE TABLE documents (
    id integer NOT NULL DEFAULT nextval('documents_id_seq'),
    key text,
    country_code text NOT NULL,
    title text NOT NULL,
    description text NOT NULL,
    content text,
    language text NOT NULL,
    link text,
    enabled boolean NOT NULL DEFAULT true,
    last_update TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    params jsonb,
    PRIMARY KEY(id)
);
COMMENT ON COLUMN documents.key  IS 'Chiave alfanumerica con cui recuperare il documento';
ALTER SEQUENCE documents_id_seq OWNED BY documents.id;
ALTER TABLE ONLY public.documents ADD CONSTRAINT documents_countries FOREIGN KEY (country_code) REFERENCES countries (code) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE ONLY public.documents ADD CONSTRAINT documents_languages FOREIGN KEY (language) REFERENCES languages (code) NOT DEFERRABLE INITIALLY IMMEDIATE;
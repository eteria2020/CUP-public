-- psql --host=127.0.0.1  --username=sharengo --password sharengo -f /srv/apps/sharengo-publicsite/data/migrations/201803221030_create_partners_telepass.sql

CREATE TABLE partners (
    id SERIAL PRIMARY KEY,
    name text NOT NULL,
    code text NOT NULL,
    params text NOT NULL,
    enabled boolean NOT NULL DEFAULT TRUE
);

INSERT INTO partners (name, code, params, enabled) VALUES ('Free2move', 'FREE2MOVE', '{"info" :{ "utm_source" : "FREE2MOVE"} }', true);
INSERT INTO partners (name, code, params, enabled) VALUES ('Telepass', 'telepass', '{"payments" :{ "uri" : "https://api-dev.urbi.co", "authorization" : "sharengo_test_key"} }', true);
INSERT INTO partners (name, code, params, enabled) VALUES ('Nugo', 'nugo', '{"payments" :{ "uri" : "https://api-dev.nugo.co", "authorization" : "sharengo_test_key"} }', true);

CREATE TABLE partners_customers (
    id SERIAL PRIMARY KEY,
    partner_id integer NOT NULL,
    customer_id integer NOT NULL,
    inserted_ts timestamp NOT NULL,
    disabled_ts timestamp DEFAULT NULL
);

ALTER TABLE partners_customers ALTER COLUMN inserted_ts SET DEFAULT now();

ALTER TABLE ONLY partners_customers ADD CONSTRAINT customer_id FOREIGN KEY (customer_id) REFERENCES customers(id);
ALTER TABLE ONLY partners_customers ADD CONSTRAINT partner_id  FOREIGN KEY (partner_id)  REFERENCES partners(id);

ALTER TABLE ONLY contracts ADD COLUMN partner_id integer;
ALTER TABLE ONLY contracts ADD CONSTRAINT partner_id  FOREIGN KEY (partner_id)  REFERENCES partners(id);

ALTER TABLE ONLY contracts ADD COLUMN priority integer NOT NULL DEFAULT 0;

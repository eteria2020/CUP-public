CREATE TABLE partners (
    id SERIAL PRIMARY KEY,
    name text NOT NULL,
    code text NOT NULL,
    params text NOT NULL,
    enabled boolean NOT NULL DEFAULT TRUE
);

INSERT INTO partners (name, code, params, enabled) VALUES ('Free2move', 'FREE2MOVE', '{"info" :{ "utm_source" : "FREE2MOVE"} }', true);
INSERT INTO partners (name, code, params, enabled) VALUES ('Telepass', 'telepass', '{"payments" :{ "uri" : "https://api-dev.urbi.co", "authorization" : "sharengo_test_key"} }', true);

CREATE TABLE partners_customers (
    id SERIAL PRIMARY KEY,
    partner_id integer NOT NULL,
    customer_id integer NOT NULL,
    inserted_ts timestamp NOT NULL,
    enabled boolean NOT NULL DEFAULT TRUE
);

ALTER TABLE partners_customers ALTER COLUMN inserted_ts SET DEFAULT now();

ALTER TABLE ONLY partners_customers ADD CONSTRAINT customer_id FOREIGN KEY (customer_id) REFERENCES customers(id);
ALTER TABLE ONLY partners_customers ADD CONSTRAINT partner_id  FOREIGN KEY (partner_id)  REFERENCES partners(id);

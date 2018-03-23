CREATE TABLE partners (
    id SERIAL PRIMARY KEY,
    name text NOT NULL,
    code text NOT NULL,
    params text NOT NULL,
    enabled boolean NOT NULL
);

INSERT INTO partners (name, code, params, enabled) VALUES ('Free2move', 'FREE2MOVE', '{"info" :{ "utm_source" : "FREE2MOVE"} }', true);
INSERT INTO partners (name, code, params, enabled) VALUES ('Telepass', 'telepass', '{"payments" :{ "uri" : "https://api-dev.urbi.co", "authorization" : "sharengo_test_key"} }', true);
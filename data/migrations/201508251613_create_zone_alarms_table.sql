CREATE TABLE zone_alarms (
    id SERIAL PRIMARY KEY,
    name text NOT NULL,
    geo polygon NOT NULL
);



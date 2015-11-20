CREATE TABLE customer_notes (
    id SERIAL PRIMARY KEY,
    customer_id INT REFERENCES customers (id) NOT NULL,
    webuser_id INT REFERENCES webuser (id) NOT NULL,
    inserted_ts TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    note TEXT NOT NULL
);

ALTER TABLE customer_notes OWNER TO sharengo;

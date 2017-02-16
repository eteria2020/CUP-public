CREATE TABLE payment_script_runs (
    id SERIAL PRIMARY KEY,
    start_ts TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    end_ts TIMESTAMP(0) WITHOUT TIME ZONE
);

ALTER TABLE payment_script_runs OWNER TO sharengo;
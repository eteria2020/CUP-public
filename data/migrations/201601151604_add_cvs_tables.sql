CREATE TYPE csv_anomaly_type AS ENUM (
    'MISSING_FROM_TRANSACTIONS',
    'OUTCOME_ERROR',
    'AMOUNT_ERROR'
);

CREATE TABLE cartasi_csv_files (
    id SERIAL PRIMARY KEY,
    inserted_ts TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    filename TEXT NOT NULL,
    webuser_id INT NOT NULL REFERENCES webuser(id)
);

CREATE TABLE cartasi_csv_anomalies (
    id SERIAL PRIMARY KEY,
    inserted_ts TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    cartasi_csv_file_id INT NOT NULL REFERENCES cartasi_csv_files(id),
    type csv_anomaly_type NOT NULL,
    amount INT NOT NULL,
    resolved BOOLEAN DEFAULT false NOT NULL,
    resolved_ts TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
    webuser_id INT REFERENCES webuser(id) DEFAULT NULL,
    csv_data jsonb NOT NULL,
    transaction_id INT REFERENCES transactions(id) DEFAULT NULL,
    updates jsonb DEFAULT NULL
);

ALTER TABLE cartasi_csv_files OWNER TO sharengo;

ALTER TABLE cartasi_csv_anomalies OWNER TO sharengo;

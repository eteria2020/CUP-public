CREATE TYPE csv_anomaly_type AS ENUM (
    'MISSING_FROM_TRANSACTIONS',
    'MISSING_FROM_CSV',
    'OUTCOME_ERROR'
);

CREATE TABLE cartasi_csv_files (
    id SERIAL PRIMARY KEY,
    inserted_ts TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    filename TEXT NOT NULL,
    analyzed BOOLEAN DEFAULT false NOT NULL
);

CREATE TABLE cartasi_csv_anomalies (
    id SERIAL PRIMARY KEY,
    inserted_ts TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    cartasi_csv_file_id INT NOT NULL REFERENCES cartasi_csv_files(id),
    type csv_anomaly_type NOT NULL,
    resolved BOOLEAN DEFAULT false NOT NULL,
    csv_data jsonb DEFAULT NULL,
    transaction_id INT REFERENCES transactions(id) DEFAULT NULL,
    updates jsonb DEFAULT NULL
);

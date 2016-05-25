CREATE TABLE cartasi_csv_anomalies_notes (
    id SERIAL PRIMARY KEY,
    inserted_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    cartasi_csv_anomaly_id INT REFERENCES cartasi_csv_anomalies(id),
    webuser_id INT REFERENCES webuser(id),
    note VARCHAR(255)
);

ALTER TABLE cartasi_csv_anomalies_notes OWNER TO sharengo;

--remove column from updates from cartasi_csv_anomalies
ALTER TABLE cartasi_csv_anomalies
DROP COLUMN updates;

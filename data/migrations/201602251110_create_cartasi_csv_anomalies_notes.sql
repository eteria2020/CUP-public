CREATE TABLE cartasi_csv_anomalies_notes (
    id SERIAL PRIMARY KEY,
    inserted_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    cartasi_csv_anomaly_id INT REFERENCES cartasi_csv_anomalies(id),
    webuser_id INT REFERENCES webuser(id),
    note VARCHAR(255)
);

ALTER TABLE cartasi_csv_anomalies_notes OWNER TO sharengo;

--migrate notes from cartasi_csv_anomalies.update to the table created

INSERT INTO cartasi_csv_anomalies_notes (cartasi_csv_anomaly_id, inserted_at, note, webuser_id)
    SELECT
        cartasi_csv_anomalies.id AS anomaly_id,
        json_data.key::timestamp AS inserted_at,
        json_data.value::json->>'content' AS content,
        CAST(json_data.value::json->>'webuser' AS INT) AS webuser
    FROM cartasi_csv_anomalies, jsonb_each_text(cartasi_csv_anomalies.updates) AS json_data;

--remove column from updates from cartasi_csv_anomalies
ALTER TABLE cartasi_csv_anomalies
DROP COLUMN updates;
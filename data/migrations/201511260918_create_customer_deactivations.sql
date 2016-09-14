CREATE TYPE disabled_reason AS ENUM (
    'FIRST_PAYMENT_NOT_COMPLETED',
    'FAILED_PAYMENT',
    'INVALID_DRIVERS_LICENSE',
    'DISABLED_BY_WEBUSER'
);

CREATE TABLE customer_deactivations (
    id SERIAL PRIMARY KEY,
    inserted_ts TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    customer_id INT NOT NULL REFERENCES customers(id),
    reason disabled_reason NOT NULL,
    start_ts TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    end_ts TIMESTAMP(0) WITHOUT TIME ZONE,
    deactivator_webuser_id INT REFERENCES webuser(id),
    reactivator_webuser_id INT REFERENCES webuser(id),
    details jsonb NOT NULL
);

ALTER TABLE customer_deactivations OWNER TO sharengo;



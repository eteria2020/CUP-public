CREATE TYPE disabled_reason AS ENUM (
    'FIRST_PAYMENT_NOT_COMPLETED',
    'FAILED_PAYMENT',
    'INVALD_DRIVERS_LICENSE',
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

/**
 * Customers disabled for late payment
 */
INSERT INTO customer_deactivations
SELECT nextval('customer_deactivations_id_seq'),
    now(),
    c.id,
    'FAILED_PAYMENT',
    now(),
    NULL,
    NULL,
    NULL,
    '{"deactivation":{"trip_payment_try_id":"not available"}}'
FROM customers c
WHERE c.enabled = false
AND c.payment_able = false
AND c.first_payment_completed = true
AND c.maintainer = false;

/**
 * Customers disabled by webusers
 */
INSERT INTO customer_deactivations
SELECT nextval('customer_deactivations_id_seq'),
    now(),
    c.id,
    'DISABLED_BY_WEBUSER',
    now(),
    NULL,
    NULL,
    NULL,
    '{"deactivation":{"note":""}}'
FROM customers c
WHERE c.enabled = false
AND c.payment_able = true
AND c.first_payment_completed = true
AND c.maintainer = false;

/**
 * Customers not yet enabled after registration
 */
INSERT INTO customer_deactivations
SELECT nextval('customer_deactivations_id_seq'),
    now(),
    c.id,
    'FIRST_PAYMENT_NOT_COMPLETED',
    now(),
    NULL,
    NULL,
    NULL,
    '{"deactivation":{}}'
FROM customers c
WHERE c.enabled = false
AND c.first_payment_completed = false
AND c.maintainer = false;

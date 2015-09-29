/**
 * Check if there are any trip_payents with status = wrong_payment that do not
 * have a related trip_payment_try. This is unexpected so this will show there
 * is a problem if it returns any row. Do not proceed!
 */
SELECT *
FROM trip_payments tp
WHERE tp.status = 'wrong_payment'
AND NOT EXISTS(
    SELECT 1
    FROM trip_payment_tries tpt
    WHERE tpt.trip_payment_id = tp.id
);

/**
 * Add the column first_trip_payment_ts
 */
ALTER TABLE trip_payments ADD first_payment_try_ts TIMESTAMP(0) WITHOUT TIME ZONE;

/**
 * Set the column first_trip_payment_ts with the value from the
 * first (chronologically) trip_payment_tries that references that trip_payment
 * except for trip_payments with status = 'to_be_payed'
 */
UPDATE trip_payments tp
SET first_payment_try_ts = (
    SELECT tpt.ts
    FROM trip_payment_tries tpt
    WHERE tpt.trip_payment_id = tp.id
    AND tp.status != 'to_be_payed'
    ORDER BY tpt.id
    LIMIT 1
);

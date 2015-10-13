ALTER TABLE extra_payments ADD invoice_able BOOLEAN DEFAULT 'true';
ALTER TABLE extra_payments ADD generated_ts TIMESTAMP(0) WITHOUT TIME ZONE;

UPDATE extra_payments SET generated_ts = '2015-10-01 00:00:00';

/* check if all the extra payments present need to be invoice_able */
UPDATE extra_payments SET invoice_able = TRUE;

ALTER TABLE extra_payments ALTER COLUMN generated_ts SET NOT NULL;
ALTER TABLE extra_payments ALTER COLUMN invoice_able SET NOT NULL;
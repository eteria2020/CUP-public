ALTER TABLE extra_payments
ADD COLUMN reason_temp jsonb;

UPDATE extra_payments
SET reason_temp = ('{"' || reason || '": ' || amount::text || '}')::jsonb;

ALTER TABLE extra_payments
ALTER COLUMN reason_temp SET NOT NULL;

ALTER TABLE extra_payments
DROP COLUMN reason;

ALTER TABLE extra_payments
RENAME COLUMN reason_temp TO reason;

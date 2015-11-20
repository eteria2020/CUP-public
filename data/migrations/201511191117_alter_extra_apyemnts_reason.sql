ALTER TABLE extra_payments
ADD COLUMN reasons jsonb;

UPDATE extra_payments
SET reasons = ('{"' || reason || '": ' || amount::text || '}')::jsonb;

ALTER TABLE extra_payments
ALTER COLUMN reasons SET NOT NULL;

ALTER TABLE extra_payments
DROP COLUMN reason;

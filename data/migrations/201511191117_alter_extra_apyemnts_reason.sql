ALTER TABLE extra_payments
ADD COLUMN reasons jsonb;

CREATE TYPE extra_payments_types AS ENUM('extra', 'penalty');

ALTER TABLE extra_payments
ADD COLUMN types_temp extra_payments_types;

UPDATE extra_payments
SET reasons = ('{"' || replace(reason, E'\t', '') || '": ' || amount::text || '}')::jsonb,
    types_temp = payment_type::extra_payments_types;

ALTER TABLE extra_payments
ALTER COLUMN reasons SET NOT NULL;

ALTER TABLE extra_payments
ALTER COLUMN types_temp SET NOT NULL;

ALTER TABLE extra_payments
DROP COLUMN reason;

ALTER TABLE extra_payments
DROP COLUMN payment_type;

ALTER TABLE extra_payments
RENAME COLUMN types_temp TO payment_type;

ALTER TABLE extra_payments ADD FOREIGN KEY (customer_id) REFERENCES customers (id);
ALTER TABLE extra_payments ADD FOREIGN KEY (invoice_id) REFERENCES invoices (id);

ALTER TABLE trip_payments ADD to_be_payed_from TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT NOW();

UPDATE trip_payments SET to_be_payed_from = created_at;

ALTER TABLE trip_payments ALTER COLUMN to_be_payed_from DROP DEFAULT;
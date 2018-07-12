ALTER TABLE extra_payments  ADD COLUMN status trip_payment_status DEFAULT 'to_be_payed'::trip_payment_status NOT NULL;
COMMENT ON COLUMN extra_payments.status IS 'Stato del pagamento';

ALTER TABLE extra_payments ADD COLUMN first_extra_try_ts timestamp(0) without time zone;

ALTER TABLE extra_payments ADD COLUMN payable boolean DEFAULT true;
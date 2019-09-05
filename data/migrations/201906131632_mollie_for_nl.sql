INSERT INTO partners (id,name,code,params) VALUES (nextval('partners_id_seq'), 'Mollie','mollie','');

ALTER TABLE transactions ALTER COLUMN message TYPE text ;
ALTER TABLE transactions ALTER COLUMN message DROP DEFAULT;

ALTER TABLE contracts ADD COLUMN "param" jsonb DEFAULT null;
COMMENT ON COLUMN contracts.param IS 'contiene i parameri di pagamento (utile in Mollie)';

/* ALTER TYPE disabled_reason ADD VALUE 'EXPIRED_CREDIT_CARD'; */
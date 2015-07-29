CREATE SEQUENCE sequence_invoice_number;
SELECT setval('sequence_invoice_number', (EXTRACT(YEAR FROM now())::bigint * 10000000000));

CREATE OR REPLACE FUNCTION nextval_sequence_invoice_number()
    RETURNS TEXT
    LANGUAGE plpgsql
    AS
    $$
        DECLARE currVal bigint;
        BEGIN
            currVal := nextval('sequence_invoice_number');
            RETURN to_char((currVal / 10000000000), '9999') || '/' || to_char((currVal % 10000000000), 'FM0999999999');
        END;
    $$;

CREATE OR REPLACE FUNCTION before_insert_invoice()
    RETURNS trigger
    LANGUAGE plpgsql
    AS
    $$
        DECLARE base_val  bigint;
        BEGIN
            NEW.invoice_number := nextval_sequence_invoice_number();
            base_val := (EXTRACT(YEAR FROM now())::bigint * 10000000000);

            IF (currval('sequence_invoice_number') < base_val) THEN
                PERFORM setval('sequence_invoice_number', base_val);
                NEW.invoice_number := nextval_sequence_invoice_number();
            END IF;

            RETURN NEW;
        END;
    $$;

CREATE TYPE invoice_type AS ENUM ('FIRST_PAYMENT', 'TRIP', 'PENALTY');

CREATE TABLE invoices (
    id SERIAL PRIMARY KEY,
    invoice_number TEXT DEFAULT nextval_sequence_invoice_number(),
    customer_id integer NOT NULL,
    generated_ts timestamp(0) with time zone NOT NULL,
    content jsonb NOT NULL, #text if psql 9.1,
    version int NOT NULL,
    type invoice_type NOT NULL,
    invoice_date integer NOT NULL,
    amount int NOT NULL
);

ALTER SEQUENCE sequence_invoice_number OWNED BY invoices.invoice_number;

CREATE TRIGGER trigger_invoice_created
    BEFORE INSERT ON invoices
    FOR EACH ROW EXECUTE PROCEDURE before_insert_invoice();

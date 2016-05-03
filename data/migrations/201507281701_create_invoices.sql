CREATE TYPE invoice_type AS ENUM ('FIRST_PAYMENT', 'TRIP', 'PENALTY');

CREATE TABLE invoices (
    id SERIAL PRIMARY KEY,
    invoice_number TEXT,
    customer_id integer NOT NULL,
    generated_ts timestamp(0) with time zone NOT NULL,
    content jsonb NOT NULL, -- text if psql 9.1,
    version int NOT NULL,
    type invoice_type NOT NULL,
    invoice_date integer NOT NULL,
    amount int NOT NULL
);

CREATE SEQUENCE sequence_invoice_number OWNED BY invoices.invoice_number;

CREATE OR REPLACE FUNCTION before_insert_invoice()
    RETURNS trigger
    LANGUAGE plpgsql
    AS
    $$
        DECLARE base_val  bigint;
        DECLARE next_val bigint;
        BEGIN
            base_val := (EXTRACT(YEAR FROM now())::bigint * 10000000000);

            IF ((SELECT last_value FROM sequence_invoice_number) < base_val) THEN
                PERFORM setval('sequence_invoice_number', base_val);
            END IF;

            next_val := nextval('sequence_invoice_number');
            NEW.invoice_number := to_char((next_val / 10000000000), 'FM9999') || '/' || to_char((next_val % 10000000000), 'FM0999999999');

            RETURN NEW;
        END;
    $$;

CREATE TRIGGER trigger_invoice_created
    BEFORE INSERT ON invoices
    FOR EACH ROW EXECUTE PROCEDURE before_insert_invoice();

/**
 * Add a column to specify the code used in invoices
 */
ALTER TABLE fleets ADD int_code TEXT UNIQUE;
/**
 * Set the codes for the current fleets and set the column as NOT NULL
 * Make sure 00 is the code for Milano and 01 for Firenze
 */
UPDATE fleets SET int_code = '00' WHERE id = 1;
UPDATE fleets SET int_code = '01' WHERE id = 2;
ALTER TABLE fleets ALTER COLUMN int_code SET NOT NULL;

/**
 * Add a column for the fleet to enable filtering and
 * easier access to this information.
 */
ALTER TABLE invoices ADD fleet_id INTEGER REFERENCES fleets(id);
/**
 * Set the current invoices with fleet for Milano. Make sure 1 is for Milano.
 * Now that it is populated, set the column as NOT NULL.
 */
UPDATE invoices SET fleet_id = 1;
ALTER TABLE invoices ALTER COLUMN fleet_id SET NOT NULL;
/**
 * Drop the trigger that was called when a new row was inserted.
 */
DROP TRIGGER IF EXISTS trigger_invoice_created ON invoices;
/**
 * Drop the function that is called when a new row is inserted.
 * This function used to generate the invoice_number value.
 */
DROP FUNCTION IF EXISTS before_insert_invoice();
/**
 * Now that the invoice_number value is generated in php and not by postgresql,
 * it makes sense to set the column as NOT NULL.
 */
ALTER TABLE invoices ALTER COLUMN invoice_number SET NOT NULL;

/**
 * Add UNIQUE key to invoice_number to avoid flushing multiple invoices at the
 * same time that would generate multiple invoice numbers with the same value.
 */
ALTER TABLE invoices ADD CONSTRAINT unique_invoice_number UNIQUE (invoice_number);

/**
 * Create new sequences for the two fleets to generate the invoice_number.
 * First set fleets.code to UNIQUE as it will be used as suffix in sequence name.
 * Make sure 20150100000001 is right for Firenze.
 */
ALTER TABLE fleets ADD CONSTRAINT unique_code UNIQUE (code);
CREATE SEQUENCE sequence_invoice_number_mi;
CREATE SEQUENCE sequence_invoice_number_fi START 20150100000001;

ALTER SEQUENCE sequence_invoice_number_mi OWNER TO sharengo;
ALTER SEQUENCE sequence_invoice_number_fi OWNER TO sharengo;
/**
 * DROP SEQUENCE IF EXISTS sequence_invoice_number_mi;
 * DROP SEQUENCE IF EXISTS sequence_invoice_number_fi;
 */

/**
 * Create function that sets the value for the sequence for Milano to the
 * value of the current used sequence.
 * Once used, remove the function.
 */
CREATE OR REPLACE FUNCTION set_sequence_invoice_number_mi_start()
    RETURNS void
    LANGUAGE plpgsql
    AS
    $$
        BEGIN
            PERFORM setval('sequence_invoice_number_mi', (SELECT last_value FROM sequence_invoice_number));
        END;
    $$;
SELECT set_sequence_invoice_number_mi_start();
DROP FUNCTION set_sequence_invoice_number_mi_start();



CREATE OR REPLACE FUNCTION before_insert_invoice()
    RETURNS trigger
    LANGUAGE plpgsql
    AS
    $$
        DECLARE code TEXT;
        DECLARE curr_val bigint;
        DECLARE base_val bigint;
        DECLARE next_val bigint;
        BEGIN
            code := (SELECT f.code FROM fleets f WHERE f.id = NEW.fleet_id);
            EXECUTE format('SELECT last_value FROM sequence_invoice_number_%s',lower(code)) INTO curr_val;
            base_val := (EXTRACT(YEAR FROM now())::bigint * 10000000000);

            IF (curr_val < base_val) THEN
                PERFORM setval('sequence_invoice_number_' || code, base_val);
            END IF;

            next_val := nextval('sequence_invoice_number_' || code);
            NEW.invoice_number := to_char((next_val / 10000000000), 'FM9999') || '/' || to_char((next_val % 10000000000), 'FM0999999999');

            RETURN NEW;
        END;
    $$;

ALTER FUNCTION before_insert_invoice() OWNER TO sharengo;

CREATE TRIGGER trigger_invoice_created
    BEFORE INSERT ON invoices
    FOR EACH ROW EXECUTE PROCEDURE before_insert_invoice();








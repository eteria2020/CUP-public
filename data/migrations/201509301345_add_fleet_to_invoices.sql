/**
 * Add a column to specify the code used in invoices
 */
ALTER TABLE fleets ADD int_code TEXT;
/**
 * Set the codes for the current fleets and set the column as NOT NULL
 * @type {[type]}
 */
UPDATE fleets SET int_code = '00' WHERE id = 1;
UPDATE fleets SET int_code = '01' WHERE id = 2;
ALTER TABLE fleets ALTER COLUMN int_code SET NOT NULL;

/**
 * Add a column for the fleet to enable filtering and
 * easier access to this information.
 */
ALTER TABLE invoices ADD fleet_int_code TEXT;
/**
 * Set the current invoices with code for Milano.
 * Now that it is populated, set the column as NOT NULL.
 */
UPDATE invoices SET fleet_int_code = '00';
ALTER TABLE invoices ALTER COLUMN fleet_int_code SET NOT NULL;
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

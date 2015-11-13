/**
 * Create table for payments for customers_bonus from packages
 */
CREATE TABLE bonus_package_payments (
    id SERIAL PRIMARY KEY,
    customer_id INT NOT NULL REFERENCES customers(id),
    bonus_id INT NOT NULL REFERENCES customers_bonus(id),
    package_id INT NOT NULL REFERENCES customers_bonus_packages(id),
    fleet_id INT NOT NULL REFERENCES fleets(id),
    transaction_id INT NOT NULL REFERENCES transactions(id),
    invoice_id INT REFERENCES invoices(id) DEFAULT NULL,
    invoiced_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
    amount INT NOT NULL,
    inserted_ts TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
);

/**
 * Change ownership to sharengo
 */
ALTER TABLE bonus_package_payments OWNER TO sharengo;
ALTER SEQUENCE bonus_package_payments_id_seq OWNER TO sharengo;

/**
 * Populate the table with existing data from other tables
 */
INSERT INTO bonus_package_payments
SELECT nextval('bonus_package_payments_id_seq'::regclass),
    c.id,
    cb.id,
    cb.package_id,
    c.fleet_id,
    cb.transaction_id,
    cb.invoice_id,
    cb.invoiced_at,
    cb.total,
    now()
FROM customers_bonus cb
LEFT JOIN customers c ON c.id = cb.customer_id
WHERE cb.package_id IS NOT NULL
ORDER BY cb.insert_ts ASC;

/**
 * Update structure of customers_bonus table
 */
ALTER TABLE customers_bonus
DROP COLUMN IF EXISTS transaction_id,
DROP COLUMN IF EXISTS invoice_id,
DROP COLUMN IF EXISTS invoiced_at,
DROP COLUMN IF EXISTS package_id;

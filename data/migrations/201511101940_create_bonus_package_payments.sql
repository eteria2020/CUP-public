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


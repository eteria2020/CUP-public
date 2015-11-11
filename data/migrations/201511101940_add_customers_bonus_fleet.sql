/**
 * Create the column that references the fleet corresponding to the purchase
 */
ALTER TABLE customers_bonus
ADD payment_fleet_id INTEGER REFERENCES fleets(id);

/**
 * For any customers_bonus that has an invoice, set the fleet as the one in the
 * invoice
 */
UPDATE customers_bonus AS cb
SET payment_fleet_id = i.id
FROM invoices AS i
WHERE i.id = cb.invoice_id;

/**
 * For any customers_bonus that still does not have a payment_fleet_id, set it
 * as the current preferred fleet of the customer (this was the expected
 * behaviour)
 */
UPDATE customers_bonus AS cb
SET payment_fleet_id = c.fleet_id
FROM customers AS c
WHERE cb.package_id IS NOT NULL
AND cb.payment_fleet_id IS NULL
AND c.id = cb.customer_id;

/**
 * Add foreign key to package_id
 */
ALTER TABLE customers_bonus
ADD FOREIGN KEY(package_id) REFERENCES customers_bonus_packages(id);

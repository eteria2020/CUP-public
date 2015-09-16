/**
 * Fix missing reference to customers from invoices
 */
ALTER TABLE invoices ADD FOREIGN KEY (customer_id) REFERENCES customers (id);

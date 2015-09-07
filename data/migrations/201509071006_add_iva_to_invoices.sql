ALTER TABLE invoices ADD iva integer;
UPDATE invoices SET iva = 22;
ALTER TABLE invoices ALTER COLUMN iva SET NOT NULL;

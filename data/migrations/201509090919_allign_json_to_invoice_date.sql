/**
 * updates the content column's field "invoice_date"
 * with the value of the invoice_date column by:
 * - converting the jsonb content to text
 * - using a regex to find the "invoice_date" field
 * - replacing that field with the value from the invoice_date column
 * - converting the result back to jsonb and storing it
 */
UPDATE invoices SET content = regexp_replace(content::text, '"invoice_date": [0-9]+', '"invoice_date": ' || invoice_date || '')::jsonb;

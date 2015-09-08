/**
 * Adds a column for iva with value 22 and sets it to NOT NULL
 */
ALTER TABLE invoices ADD iva integer;
UPDATE invoices SET iva = 22;
ALTER TABLE invoices ALTER COLUMN iva SET NOT NULL;

/**
 * Adds the iva value to the content column by:
 * - converting the jsonb to text (without first '{')
 * - appending iva to the front
 * - converting result back to jsonb
 */
UPDATE invoices
    SET content = (
        '{"iva":22,' ||
        substring(
            content::text
            from 2
            for (char_length(content::text) - 1)
        )
    )::jsonb;

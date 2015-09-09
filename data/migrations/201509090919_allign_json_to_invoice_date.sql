UPDATE invoices SET content = regexp_replace(content::text, '"invoice_date": [0-9]+', '"invoice_date": ' || invoice_date || '')::jsonb;

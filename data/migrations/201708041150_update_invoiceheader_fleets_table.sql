/* 
 * CSD1182 - update share capital for CS Group SpA (Modena)
 */
UPDATE 
    fleets 
SET 
    invoice_header = REPLACE (invoice_header, '3.500.000,00 i.v.', '8.150.000,00 i.v.')
WHERE
    id = 4;
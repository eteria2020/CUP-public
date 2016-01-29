/**
 * Fixes an error where status was not set correctly after invoice was generated
 */
UPDATE trip_payments
SET status = 'invoiced'
WHERE invoice_id IS NOT NULL;

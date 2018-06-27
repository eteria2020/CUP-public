CREATE OR REPLACE FUNCTION partnerData (_name TEXT)
RETURNS text AS $$
DECLARE
   c_lead integer;
   c_subscribers integer;
   response text;
BEGIN
   c_lead := (SELECT count(*) FROM customers c WHERE c.first_payment_completed = FALSE OR c.registration_completed = FALSE);
   
   c_subscribers := (SELECT count(*) FROM customers c, customers_bonus b, promo_codes p WHERE c.id = b.customer_id AND b.promocode_id = p.id AND c.first_payment_completed = TRUE AND c.registration_completed = TRUE AND p.promocode like '%' || _name || '%');

   response := (SELECT concat(c_lead, ',', c_subscribers));
   RETURN response;
END;
$$ LANGUAGE plpgsql;
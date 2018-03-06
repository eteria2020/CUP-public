CREATE OR REPLACE FUNCTION partnerData (name TEXT)
RETURNS text AS $$
DECLARE
   c_lead integer;
   c_subscribers integer;
   response text;
BEGIN
   c_lead := (SELECT count(*) FROM customers c WHERE enabled = FALSE);
   
   c_subscribers := (select count(*) FROM customers c, promo_codes p, customers_bonus b WHERE c.id = b.customer_id AND b.promocode_id = p.id AND p.id IN (160,161,162,163));

   response := (SELECT concat(c_lead, ',', c_subscribers));
   RETURN response;
END;
$$ LANGUAGE plpgsql;
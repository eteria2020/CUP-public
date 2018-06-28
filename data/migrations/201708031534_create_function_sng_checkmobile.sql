/* 
 * This function check if the mobile number is already present.
 * The mobile number is checked removing all non-numeric chars and starting from right to left.
 * Using LIKE it's possible to check also the numbers without dial code.
 */
/**
 * Author:  Alessandra Citterio
 * Created: 21-ago-2017
 */
CREATE OR REPLACE FUNCTION sng_checkMobile(p_mobile varchar)
RETURNS integer AS $present$
DECLARE
	present integer;
BEGIN
   SELECT 
   		COUNT(*) INTO present 
   FROM 
   		Customers
   WHERE
                REVERSE(REGEXP_REPLACE(mobile, '[^0-9]+', '', 'g')) LIKE CONCAT(REVERSE(p_mobile),'%');
   RETURN 
   		present;
END;
$present$ LANGUAGE plpgsql;

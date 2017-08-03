/* 
 * This function check if the mobile number is already present.
 * The mobile number is checked replacing spaces
 */
/**
 * Author:  Alessandra Citterio
 * Created: 3-ago-2017
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
   		REPLACE(mobile,' ','') = p_mobile;
   RETURN 
   		present;
END;
$present$ LANGUAGE plpgsql;

CREATE FUNCTION "public"."addBonusSilverList" () RETURNS void AS 'DECLARE
        silver_customers RECORD;

 all_silver_customers CURSOR
 FOR SELECT id FROM customers WHERE silver_list = TRUE;

    BEGIN
        OPEN all_silver_customers;
        LOOP
            FETCH all_silver_customers INTO silver_customers;

            EXIT WHEN NOT FOUND;

            INSERT INTO customers_bonus VALUES (nextval(''customersbonus_id_seq''),silver_customers.id,null,''t'',now(),now(),1000,1000,''promo'',null,(select date_trunc(''month'', current_date)),null,(SELECT (date_trunc(''MONTH'', now()) + INTERVAL ''1 MONTH - 1 day'')::DATE),''Bonus Silver List'',null,null,null,null,null);
		END LOOP;

	CLOSE all_silver_customers ;

	END;' LANGUAGE "plpgsql"



ALTER TABLE "public"."customers" ADD COLUMN "silver_list" boolean NOT NULL DEFAULT false;

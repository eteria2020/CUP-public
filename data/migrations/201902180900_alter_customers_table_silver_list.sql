ALTER TABLE "public"."customers" ADD COLUMN "silver_list" boolean NOT NULL DEFAULT false;

INSERT INTO "public"."configurations" ("id","slug","config_key","config_value","config_spec") VALUES (nextval('configurations_id_seq'::regclass),'psqlfunc','silver_list','1500',NULL);

CREATE FUNCTION "public"."addBonusSilverList" () RETURNS void AS $BODY$ DECLARE
        silver_customers RECORD;

 all_silver_customers CURSOR
 FOR SELECT id FROM customers WHERE silver_list = TRUE;

    BEGIN
        OPEN all_silver_customers;
        LOOP
            FETCH all_silver_customers INTO silver_customers;

            EXIT WHEN NOT FOUND;

            INSERT INTO customers_bonus VALUES (nextval('customersbonus_id_seq'),silver_customers.id,null,'t',now(),now(),(SELECT CAST(config_value as INTEGER) FROM configurations WHERE config_key = 'silver_list'),(SELECT CAST(config_value as INTEGER)FROM configurations WHERE config_key = 'silver_list'),'promo',null,(select date_trunc('month', current_date)),null,(SELECT ((date_trunc('MONTH', now()) + INTERVAL '1 MONTH - 1 day')::DATE) || ' 23:59:59')::timestamp,'Bonus Silver List',null,null,null,null,null);
		END LOOP;

	CLOSE all_silver_customers ;

	END;$BODY$ LANGUAGE "plpgsql"


CREATE OR REPLACE FUNCTION "public"."notify_customer_update" () RETURNS trigger AS 'DECLARE
BEGIN
		PERFORM pg_notify(CAST(''customer_update'' AS text),CAST(COALESCE(OLD.email, '''') AS text) || '';'' || CAST(COALESCE(OLD.mobile, '''') AS text) || '';'' || CAST(COALESCE(OLD.address, '''') AS text) || '';'' || CAST(NEW.id AS text) || '';'' || CAST(COALESCE(NEW.surname, '''') AS text)|| '';'' ||CAST(COALESCE(NEW.name, '''') AS text) || '';'' || CAST(COALESCE(NEW.tax_code, '''') AS text) || '';'' || CAST(COALESCE(NEW.vat, '''') AS text) || '';'' || CAST(COALESCE(NEW.gender, '''') AS text) || '';'' || CAST(COALESCE(NEW.birth_town, '''') AS text) || '';'' || CAST(COALESCE(NEW.mobile, '''') AS text) || '';'' || CAST(COALESCE(NEW.birth_country, '''') AS text) || '';'' || CAST(COALESCE(NEW.birth_province, '''') AS text) || '';'' || CAST(NEW.pin AS text) || '';'' || CAST(COALESCE(NEW.language, '''') AS text) || '';'' || CAST(COALESCE(NEW.driver_license_firstname, '''') AS text) || '';'' || CAST(COALESCE(NEW.driver_license_surname, '''') AS text) || '';'' || CAST(COALESCE(NEW.driver_license, '''') AS text) || '';'' || CAST(NEW.driver_license_expire AS text) || '';'' || CAST(COALESCE(NEW.driver_license_country, '''') AS text) || '';'' || CAST(NEW.enabled AS text) || '';'' || CAST(NEW.gold_list AS text) || '';'' || CAST(NEW.silver_list AS text) || '';'' || CAST(NEW.maintainer AS text) || '';'' || CAST(NEW.first_payment_completed AS text) || '';'' || CAST(NEW.discount_rate AS text) || '';'' || CAST(NEW.registration_completed AS text) || '';'' || CAST(COALESCE(NEW.address, '''') AS text) || '';'' || CAST(COALESCE(NEW.email, '''') AS text) || '';'' || CAST(COALESCE(NEW.town, '''') AS text) || '';'' || CAST(COALESCE(NEW.zip_code, '''') AS text) || '';'' || CAST(COALESCE(NEW.country, '''') AS text) || '';'' || CAST(COALESCE(NEW.province , '''') AS text) || '';'' || CAST(NEW.fleet_id AS text) || '';'' || CAST(NEW.privacy_condition AS text) || '';'' || CAST(NEW.payment_able AS text) || '';'' || CAST(NEW.general_condition2 AS text) || '';'' || CAST(NEW.general_condition1 AS text) || '';'' || CAST(NEW.privacy_information AS text) || '';'' || CAST(NEW.driver_license_foreign AS text));

	RETURN NEW;

END;' LANGUAGE "plpgsql" COST 100
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER;


CREATE TRIGGER trigger_customer_update AFTER UPDATE ON customers FOR EACH ROW  EXECUTE PROCEDURE notify_customer_update();

CREATE OR REPLACE FUNCTION "public"."notify_customer_insert" () RETURNS trigger AS 'DECLARE
BEGIN
		PERFORM pg_notify(CAST(''customer_insert'' AS text), CAST(NEW.id AS text) || '';'' || CAST(COALESCE(NEW.email, '''') AS text) || '';'' || CAST(NEW.pin AS text)|| '';'' || CAST(NEW.fleet_id AS text) || '';'' || CAST(NEW.privacy_condition AS text) || '';'' || CAST(NEW.payment_able AS text) );

	RETURN NEW;

END;' LANGUAGE "plpgsql" COST 100
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER;

CREATE TRIGGER trigger_customer_insert AFTER INSERT ON customers FOR EACH ROW  EXECUTE PROCEDURE notify_customer_insert();
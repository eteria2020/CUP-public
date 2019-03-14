ALTER TABLE cars_info ADD COLUMN insurance_company TEXT;
ALTER TABLE cars_info ADD COLUMN insurance_number TEXT;
ALTER TABLE cars_info ADD COLUMN insurance_valid_from TIMESTAMP WITH TIME ZONE;
ALTER TABLE cars_info ADD COLUMN insurance_expiry TIMESTAMP WITH TIME ZONE;

INSERT INTO configurations (id, slug, config_key, config_value, config_spec) VALUES (nextval('configurations_id_seq'), 'car', 'cars_info_insurance', 'true', '[{"company":"ALLIANZ", "number":"00273424346"},{"company":"GENERALI", "number":"028303439"}]');

COMMENT ON COLUMN cars_info.insurance_company IS 'Compagnia assicurativa';
COMMENT ON COLUMN cars_info.insurance_number  IS 'Numero polizza assicurativa';
COMMENT ON COLUMN cars_info.insurance_valid_from IS 'Data di decorrenza polizza assicurativa';
COMMENT ON COLUMN cars_info.insurance_expiry  IS 'Data scadenza polizza assicurativa';

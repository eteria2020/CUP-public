ALTER TABLE promo_codes_info ADD bonus_valid_from TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL;
ALTER TABLE promo_codes_info ADD bonus_valid_to TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL;

/* le date sono da verificare */
UPDATE promo_codes_info SET bonus_valid_from = '2015-01-01 00:00:00', bonus_valid_to = '2015-12-31 23:59:59';

ALTER TABLE promo_codes_info ALTER COLUMN bonus_valid_from SET NOT NULL;
ALTER TABLE promo_codes_info ALTER COLUMN bonus_valid_to SET NOT NULL;
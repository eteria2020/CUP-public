ALTER TABLE trips ALTER COLUMN timestamp_end DROP NOT NULL;
ALTER TABLE trips ALTER COLUMN km_end DROP NOT NULL;
ALTER TABLE trips ALTER COLUMN battery_end DROP NOT NULL;
ALTER TABLE trips ALTER COLUMN longitude_end DROP NOT NULL;
ALTER TABLE trips ALTER COLUMN latitude_end DROP NOT NULL;

ALTER TABLE trips ALTER COLUMN geo_end DROP NOT NULL;
ALTER TABLE trips ALTER COLUMN end_tx DROP NOT NULL;
ALTER TABLE trips ALTER COLUMN park_seconds DROP NOT NULL;

ALTER TABLE trips ALTER COLUMN price_cent DROP NOT NULL;
ALTER TABLE trips ALTER COLUMN vat_cent DROP NOT NULL;
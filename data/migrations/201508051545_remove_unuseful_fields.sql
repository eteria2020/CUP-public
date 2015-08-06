ALTER TABLE trips
    DROP price_cent CASCADE,
    DROP vat_cent CASCADE;

ALTER TABLE trip_bills
    DROP cost;

CREATE OR REPLACE VIEW "Corse aperte" AS
    SELECT * FROM trips WHERE trips.timestamp_end IS NULL;

CREATE OR REPLACE VIEW "Corse di oggi" AS
    SELECT * FROM trips WHERE trips.timestamp_beginning > 'now'::text::date;
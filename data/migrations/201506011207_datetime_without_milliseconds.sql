ALTER TABLE customers ALTER inserted_ts TYPE timestamp(0) with time zone;

ALTER TABLE cars ALTER last_contact TYPE timestamp(0) with time zone;
ALTER TABLE cars ALTER last_location_time TYPE timestamp(0) with time zone;

ALTER TABLE trips ALTER timestamp_beginning TYPE timestamp(0) with time zone;
ALTER TABLE trips ALTER beginning_tx TYPE timestamp(0) with time zone;
ALTER TABLE trips ALTER timestamp_end TYPE timestamp(0) with time zone;
ALTER TABLE trips ALTER end_tx TYPE timestamp(0) with time zone;

ALTER TABLE reservations ALTER ts TYPE timestamp(0) with time zone;
ALTER TABLE reservations ALTER beginning_ts TYPE timestamp(0) with time zone;
ALTER TABLE reservations ALTER sent_ts TYPE timestamp(0) with time zone;
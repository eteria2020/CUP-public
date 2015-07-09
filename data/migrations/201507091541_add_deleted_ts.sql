ALTER TABLE reservations ADD deleted_ts timestamp(0) with time zone;
ALTER TABLE reservations_archive ADD deleted_ts timestamp(0) with time zone;

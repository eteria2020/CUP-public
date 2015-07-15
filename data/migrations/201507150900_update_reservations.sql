ALTER TYPE reservations_archive_reason ADD VALUE 'ALARM-OFF';

ALTER TABLE reservations ADD resent_ts timestamp(0) with time zone;
ALTER TABLE reservations_archive ADD resent_ts timestamp(0) with time zone;

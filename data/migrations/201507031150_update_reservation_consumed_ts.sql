DROP VIEW IF EXISTS "Prenotazioni ALARMS";

ALTER TABLE reservations ALTER consumed_ts TYPE timestamp(0) with time zone;

CREATE OR REPLACE VIEW "Prenotazioni ALARMS" AS
SELECT reservations.id,
reservations.ts,
reservations.car_plate,
reservations.customer_id,
reservations.beginning_ts,
reservations.active,
reservations.length,
reservations.to_send,
reservations.sent_ts,
reservations.consumed_ts
FROM reservations
WHERE reservations.active IS TRUE AND reservations.length = (-1);

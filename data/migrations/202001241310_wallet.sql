COMMENT ON TYPE car_status IS 'sharengo type: car status';
COMMENT ON TYPE cleanliness IS 'sharengo type: car cleanliness';
COMMENT ON TYPE csv_anomaly_type IS 'sharengo type: csv anomaly type';
COMMENT ON TYPE disabled_reason IS 'sharengo type: customer disable reason';
COMMENT ON TYPE extra_payments_types IS 'sharengo type: extra payments types';
COMMENT ON TYPE invoice_type IS 'sharengo type: invoice type';
COMMENT ON TYPE preauthorization_status IS 'sharengo type: preauthorization status';
COMMENT ON TYPE reservations_archive_reason IS 'sharengo type: reservations archive reason';
COMMENT ON TYPE trip_payment_status IS 'sharengo type: trip payment status';

UPDATE Fares SET motion_cost_per_minute=35, park_cost_per_minute=35, cost_steps='{"1440": 9000}' WHERE id=(SELECT max(id) FROM Fares);
UPDATE customers_bonus_packages SET buyable_until=now() WHERE code='H48';

INSERT INTO customers_bonus_packages (id, code, inserted_ts, minutes, type, valid_from, duration, valid_to, buyable_until, description, cost, notes, name, display_priority)
VALUES(
       nextval('customers_bonus_packages_id_seq'),
       'WALLET30',
       now(),
       30,
       'Ricarica',
       now(),
       90,
       null,
       '2021-12-31 00:00:00',
       'Ricarica crediti wallet 10 €',
       1000,
       'Con questa ricarica potrai guidate la tua Sharengo a 0,33 €/min. I 30 crediti acquistati si aggiungono al tuo wallet e sono utilizzabili entro i prossimi 3 mesi.',
       'WALLET30',
       30 );

INSERT INTO customers_bonus_packages (id, code, inserted_ts, minutes, type, valid_from, duration, valid_to, buyable_until, description, cost, notes, name, display_priority)
VALUES(
          nextval('customers_bonus_packages_id_seq'),
          'WALLET200',
          now(),
          200,
          'Ricarica',
          now(),
          90,
          null,
          '2021-12-31 00:00:00',
          'Ricarica crediti wallet 60 €',
          6000,
          'Con questa ricarica potrai guidate la tua Sharengo a 0,3 €/min. I 200 crediti acquistati si aggiungono al tuo wallet e sono utilizzabili entro i prossimi 3 mesi.',
          'WALLET200',
          200 );

INSERT INTO customers_bonus_packages (id, code, inserted_ts, minutes, type, valid_from, duration, valid_to, buyable_until, description, cost, notes, name, display_priority)
VALUES(
          nextval('customers_bonus_packages_id_seq'),
          'WALLET500',
          now(),
          500,
          'Ricarica',
          now(),
          90,
          null,
          '2021-12-31 00:00:00',
          'Ricarica crediti wallet 120 €',
          6000,
          'Con questa ricarica potrai guidate la tua Sharengo a 0,24 €/min. I 500 crediti acquistati si aggiungono al tuo wallet e sono utilizzabili entro i prossimi 3 mesi.',
          'WALLET500',
          500 );

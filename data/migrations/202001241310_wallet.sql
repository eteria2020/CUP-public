ALTER TYPE disabled_reason ADD VALUE 'CUSTOMER_BONUS_THRESHOLD';

COMMENT ON TYPE car_status IS 'sharengo type: car status';
COMMENT ON TYPE cleanliness IS 'sharengo type: car cleanliness';
COMMENT ON TYPE csv_anomaly_type IS 'sharengo type: csv anomaly type';
COMMENT ON TYPE disabled_reason IS 'sharengo type: customer disable reason';
COMMENT ON TYPE extra_payments_types IS 'sharengo type: extra payments types';
COMMENT ON TYPE invoice_type IS 'sharengo type: invoice type';
COMMENT ON TYPE preauthorization_status IS 'sharengo type: preauthorization status';
COMMENT ON TYPE reservations_archive_reason IS 'sharengo type: reservations archive reason';
COMMENT ON TYPE trip_payment_status IS 'sharengo type: trip payment status';
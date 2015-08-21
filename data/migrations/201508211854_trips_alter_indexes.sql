CREATE INDEX idx_trips_car_plate ON trips (car_plate ASC NULLS LAST);
CREATE INDEX idx_trips_customer_id ON trips (customer_id ASC NULLS LAST);
CREATE INDEX idx_trips_timestamp_beginning ON trips (timestamp_beginning ASC NULLS LAST);
ALTER TABLE trips ADD COLUMN parent_id integer;
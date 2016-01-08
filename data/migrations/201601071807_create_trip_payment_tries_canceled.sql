CREATE TABLE trip_payments_canceled (
    id SERIAL PRIMARY KEY,
    inserted_ts TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    webuser_id INT NOT NULL REFERENCES webuser(id),
    original_end_ts TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    trip_id INT NOT NULL REFERENCES trips(id),
    fare_id INT NOT NULL REFERENCES fares(id),
    trip_minutes INT NOT NULL,
    parking_minutes INT NOT NULL,
    discount_percentage INT NOT NULL,
    total_cost INT NOT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    to_be_payed_from TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    first_payment_try_ts TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
);

CREATE TABLE trip_payment_tries_canceled (
    id SERIAL PRIMARY KEY,
    inserted_ts TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    trip_payment_canceled_id INT NOT NULL REFERENCES trip_payments_canceled(id),
    webuser_id INT REFERENCES webuser(id),
    transaction_id INT REFERENCES transactions(id),
    ts TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    outcome VARCHAR(255) NOT NULL
);

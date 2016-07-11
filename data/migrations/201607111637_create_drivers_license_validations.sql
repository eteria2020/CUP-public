CREATE TABLE drivers_license_validations (
    id SERIAL PRIMARY KEY,
    customer_id integer REFERENCES customers (id) NOT NULL,
    valid boolean NOT NULL,
    code text NOT NULL,
    message text NOT NULL,
    generated_ts timestamp(0) with time zone NOT NULL
);

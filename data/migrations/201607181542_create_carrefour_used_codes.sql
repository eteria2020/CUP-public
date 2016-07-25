CREATE TABLE carrefour_used_codes (
    id SERIAL PRIMARY KEY,
    customer_id INT NOT NULL REFERENCES customers(id),
    customers_bonus_id INT NOT NULL REFERENCES customers_bonus(id),
    code VARCHAR(24) UNIQUE NOT NULL,
    inserted_ts TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
);

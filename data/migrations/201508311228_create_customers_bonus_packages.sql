CREATE TABLE customers_bonus_packages (
    id SERIAL PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    inserted_ts TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    minutes INT NOT NULL,
    type VARCHAR(100) NOT NULL,
    valid_from TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    duration INT DEFAULT NULL,
    valid_to TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
    buyable_until TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    description TEXT DEFAULT NULL,
    cost INT NOT NULL
    CHECK (duration IS NULL or valid_to IS NULL),
    CHECK (duration IS NOT NULL or valid_to IS NOT NULL)
);

ALTER TYPE invoice_type ADD VALUE IF NOT EXISTS 'BONUS_PACKAGE';


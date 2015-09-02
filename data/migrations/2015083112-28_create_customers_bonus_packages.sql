CREATE TABLE customers_bonus_packages (
    id SERIAL PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    minutes INT NOT NULL,
    type VARCHAR(100) NOT NULL,
    valid_from TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    duration INT DEFAULT NULL,
    valid_to TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
    description TEXT DEFAULT NULL,
    cost INT NOT NULL
    CHECK (duration IS NULL or valid_to IS NULL),
    CHECK (duration IS NOT NULL or valid_to IS NOT NULL)
);

INSERT INTO customers_bonus_packages VALUES (
    nextval('customers_bonus_packages_id_seq'::regClass),
    'CODE01',
    1000,
    'promo',
    '15-10-2015 0:00:00',
    null,
    '31-12-2015 23:59:59',
    'Pacchetto bonus da 1000 minuti',
    10000
)

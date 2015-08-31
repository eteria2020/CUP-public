CREATE TABLE customers_bonus_info (
    id SERIAL PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    total INT NOT NULL,
    type VARCHAR(100) NOT NULL,
    valid_from TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    length INT DEFAULT NULL,
    valid_to TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
    description TEXT DEFAULT NULL
    CHECK (length IS NULL or valid_to IS NULL),
    CHECK (length IS NOT NULL or valid_to IS NOT NULL)
);

INSERT INTO customers_bonus_info VALUES (
    nextval('customers_bonus_info_id_seq'::regClass),
    'CODE01',
    1000,
    'promo',
    '15-10-2015 0:00:00',
    null,
    '31-12-2015 23:59:59',
    'Pacchetto bonus da 1000 minuti'
)

INSERT INTO promo_codes_info Values(
    nextval('promocodesinfo_id_seq'::regclass),
    NULL,
    true,
    CURRENT_TIMESTAMP,
    'promo',
    0,
    '2015-10-28 0:00:00',
    NULL,
    '2015-12-31 23:59:59',
    100,
    '2015-01-01 00:00:00',
    '2015-12-31 23:59:59'
);

/**
 * Verify that 7 is the id of the above promo_codes_info
 */
INSERT INTO promo_codes Values(
    nextval('promocodes_id_seq'::regclass),
    7,
    'FITEST',
    'Iscrizione ad 1 euro per soci Electra',
    true
);

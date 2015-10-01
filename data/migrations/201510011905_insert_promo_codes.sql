INSERT INTO promo_codes_info (id, active, insert_ts, type, valid_from, valid_to, overridden_subscription_cost) VALUES
(nextval('promocodesinfo_id_seq'::regclass),true, '2015-10-02 00:00:00', 'promo', '2015-10-02 00:00:00', '2016-10-02 00:00:00', 100)


INSERT INTO promo_codes Values(
    nextval('promocodes_id_seq'::regclass),
    , # usare l'id del promo_codes_info appena creato
    'BKGO',
    'Iscrizione ad 1 euro per soci BikeMi',
    true
);

INSERT INTO promo_codes Values(
    nextval('promocodes_id_seq'::regclass),
    , # usare l'id del promo_codes_info appena creato
    'ELFO',
    'Iscrizione ad 1 euro per soci Teatro dell''Elfo Puccini',
    true
);

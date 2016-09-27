CREATE TABLE promo_codes_once (
    id integer NOT NULL,
    promocodesinfo_id integer NOT NULL,
    customer_id integer,
    promocode character varying(255) NOT NULL,
    used_ts timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    customer_bonus_id integer
);

ALTER TABLE ONLY promo_codes_once ADD CONSTRAINT promo_codes_once_pkey PRIMARY KEY (id);

CREATE INDEX idx_eec57fa941afebc1 ON promo_codes_once USING btree (promocodesinfo_id);

CREATE INDEX idx_eec57fa99395c3f3 ON promo_codes_once USING btree (customer_id);

CREATE UNIQUE INDEX uniq_eec57fa97c786e06 ON promo_codes_once USING btree (promocode);

ALTER TABLE ONLY promo_codes_once ADD CONSTRAINT fk_eec57fa941afebc1 FOREIGN KEY (promocodesinfo_id) REFERENCES promo_codes_info(id);

ALTER TABLE ONLY promo_codes_once ADD CONSTRAINT fk_eec57fa945911549 FOREIGN KEY (customer_bonus_id) REFERENCES customers_bonus(id);

ALTER TABLE ONLY promo_codes_once ADD CONSTRAINT fk_eec57fa99395c3f3 FOREIGN KEY (customer_id) REFERENCES customers(id);

ALTER TABLE ONLY promo_codes_once ADD CONSTRAINT promo_codes_once_unique UNIQUE (promocode);
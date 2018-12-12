/* Tab. vat */
CREATE TABLE vat (
  id integer NOT NULL,
  description text NOT NULL,
  code text NOT NULL,
  percentage integer NOT NULL
);

ALTER TABLE vat OWNER TO sharengo;

ALTER TABLE ONLY vat ADD CONSTRAINT vat_pkey PRIMARY KEY (id);
COMMENT ON TABLE vat IS 'Valude Added Tax (IVA)';

COMMENT ON COLUMN vat.description IS 'Descrizione IVA (VAT = Valude Added Tax)';
COMMENT ON COLUMN vat.code IS 'Codice Gamma IVA';
COMMENT ON COLUMN vat.percentage IS 'Percentuale IVA';

INSERT INTO vat (id, description, code, percentage) VALUES (1,'Art. 15 escluso', 'DB', 0);

/* Tab. extra_payments */
ALTER TABLE extra_payments ADD COLUMN vat_id integer;
COMMENT ON COLUMN extra_payments.vat_id IS 'Aliquota vat';
ALTER TABLE ONLY extra_payments ADD CONSTRAINT fk_vat_id FOREIGN KEY (vat_id) REFERENCES vat(id);

/* Tab. penalties */
ALTER TABLE penalties ADD COLUMN vat_id integer;
COMMENT ON COLUMN penalties.vat_id IS 'Aliquota IVA';
ALTER TABLE ONLY penalties ADD CONSTRAINT fk_vat_id FOREIGN KEY (vat_id) REFERENCES vat(id);

UPDATE penalties SET vat_id=1 WHERE id IN (6,7,10,18,19,20,23,24,25,28,29,30,32,36,37);
UPDATE penalties SET reason = 'Addebito rimozione' WHERE id=34;
UPDATE penalties SET reason = 'Soccorso stradale: per danni causati dal Cliente, con o senza controparte e perché il Cliente non avendo osservato il segnale di riserva ha lasciato il veicolo con carica/autonomia inferiore al 5 %' WHERE id=11;
UPDATE penalties SET reason = 'Recupero del veicolo per responsabilità del Cliente perché fuori dall''area operativa della città o batteria scarica' WHERE id=21;
UPDATE penalties SET amount=5000 WHERE id IN (8,22);

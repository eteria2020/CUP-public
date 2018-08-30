CREATE TABLE maintenance_locations ( 
    id integer NOT NULL,
    location text NOT NULL,
	fleet_id integer NOT NULL,
    enabled boolean NOT NULL
);

ALTER TABLE maintenance_locations OWNER TO sharengo;


CREATE SEQUENCE maintenance_locations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE maintenance_locations_id_seq OWNER TO sharengo;


ALTER SEQUENCE maintenance_locations_id_seq OWNED BY maintenance_locations.id;

ALTER TABLE ONLY maintenance_locations ALTER COLUMN id SET DEFAULT nextval('maintenance_locations_id_seq'::regclass);

ALTER TABLE ONLY maintenance_locations
    ADD CONSTRAINT maintenance_locations_pkey PRIMARY KEY (id);
	
ALTER TABLE ONLY maintenance_locations
    ADD CONSTRAINT fk_maintenance_locations_fleet FOREIGN KEY (fleet_id) REFERENCES fleets(id);
	
	


INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Milano - Officina di via Guido da Velate 9 (codice GDV)', 1, TRUE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Milano - Carrozzeria GTR Car Service, via Polidoro da Caravaggio (codice GTR)',1, TRUE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Milano - Deposito carrozzeria GTR Car Service, via Turati, Pero (codice GTR)', 1, TRUE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Milano - Carrozzeria Midicar, via Ornato (codice MID)', 1, TRUE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Milano - Carrozzeria Idone, via Tiepolo (codice IDO)', 1, TRUE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Milano - Carrozzeria Romauto, via Dottesio (codice ROM)', 1, TRUE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Milano - Carrozzeria Pennestri, via Portaluppi (codice PEN)', 1, TRUE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Milano - Carrozzeria DamianiCar, viale Murillo (codice DAM)', 1, TRUE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Milano - Carrozzeria Brima, via delle Brughiere, Garbagnate Milanese (codice BRI)', 1, TRUE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Milano - garage velasca', 1, FALSE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Milano - garage sant''ambrogio', 1, FALSE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Livorno', 1, FALSE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Milano - Milano', 1, FALSE);

INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Firenze - Carrozzeria Merciai, via del Pratellino 27/31 50124', 2, TRUE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Firenze - Rugi, via Gaetano Salvemini 3F, 50058 Signa', 2, TRUE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Firenze - Rugi, via dei Colli 188, 50058 Signa', 2, TRUE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Firenze - Sede, Piazza Eugenio Artom 12, 500127', 2, TRUE);

INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Roma - Officina Energeko Srl (Officina interna di riferimento), Via Gregorio VII, 37 - 000165 Roma', 3, TRUE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Roma - Carrozzeria Lucarelli, Via della Magliana, 642 – 00148', 3, TRUE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Roma - Carrozzeria Moderna, Via Vecchia di Napoli, 219/223 – 00049 Velletri', 3, TRUE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Roma - Carrozzeria Lucioli Franco, Via Prospero Intorcetta, 60 - 00126', 3, TRUE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Roma - Carrozzeria Assistenza car service, Via prenestina nuova km 0.200', 3, TRUE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Roma - Carrozzeria Ventura & Bianchini, SEDE VIA OSTIENSE, 999 00144 - Via Guglielmo Massaia, 17 - Ostiense, 999', 3, TRUE);

INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Modena - GL Car via Felice Cavallotti n 29 Formigine', 4, TRUE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Modena - Carrozzeria Special via Felice Cavallotti Formigine', 4, TRUE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Modena - Carrozzeria Doretto via Viazza II Tronco Ubersetto di Fiorano', 4, TRUE);

INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Milano - Auto mantenuta in strada', 1, TRUE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Firenze - Auto mantenuta in strada', 2, TRUE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Roma - Auto mantenuta in strada', 3, TRUE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Modena - Auto mantenuta in strada', 4, TRUE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Milano - Other (codice OTH)', 1, TRUE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Firenze - Other (codice OTH)', 2, TRUE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Roma - Other (codice OTH)', 3, TRUE);
INSERT INTO maintenance_locations VALUES (NEXTVAL('maintenance_locations_id_seq'),  'Modena - Other (codice OTH)', 4, TRUE);
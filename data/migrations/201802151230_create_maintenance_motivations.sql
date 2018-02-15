CREATE TABLE maintenance_motivations (
  id SERIAL PRIMARY KEY,
  description text NOT NULL,
  enabled boolean NOT NULL
);

INSERT INTO maintenance_motivations VALUES (nextval('maintenance_motivations_id_seq'),'Nessuna Motivazione',true);
INSERT INTO maintenance_motivations VALUES (nextval('maintenance_motivations_id_seq'),'Auto da carro per officina su indicazione service',true);
INSERT INTO maintenance_motivations VALUES (nextval('maintenance_motivations_id_seq'),'Auto da carro per batteria scarica',true);
INSERT INTO maintenance_motivations VALUES (nextval('maintenance_motivations_id_seq'),'Da portare in carrozzeria',true);
INSERT INTO maintenance_motivations VALUES (nextval('maintenance_motivations_id_seq'),'Auto in officina',true);
INSERT INTO maintenance_motivations VALUES (nextval('maintenance_motivations_id_seq'),'Auto marciante da portare in officina',true);
INSERT INTO maintenance_motivations VALUES (nextval('maintenance_motivations_id_seq'),'Ingresso in flotta',true);
INSERT INTO maintenance_motivations VALUES (nextval('maintenance_motivations_id_seq'),'Problema IT',true);
INSERT INTO maintenance_motivations VALUES (nextval('maintenance_motivations_id_seq'),'Lista interventi (Ex Danno 2)',true);
INSERT INTO maintenance_motivations VALUES (nextval('maintenance_motivations_id_seq'),'Auto in Carrozzeria',true);
INSERT INTO maintenance_motivations VALUES (nextval('maintenance_motivations_id_seq'),'Rimossa',true);
INSERT INTO maintenance_motivations VALUES (nextval('maintenance_motivations_id_seq'),'Wash out (da lavare)',true);
INSERT INTO maintenance_motivations VALUES (nextval('maintenance_motivations_id_seq'),'Non pertinente',true);
INSERT INTO maintenance_motivations VALUES (nextval('maintenance_motivations_id_seq'),'Sinistro stradale da definire',true);
INSERT INTO maintenance_motivations VALUES (nextval('maintenance_motivations_id_seq'),'Sinistro stradale Attivo',true);
INSERT INTO maintenance_motivations VALUES (nextval('maintenance_motivations_id_seq'),'Sinistro stradale Passivo',true);

ALTER TABLE cars_maintenance ADD column motivation INT DEFAULT 1 REFERENCES maintenance_motivations(id);
CREATE SEQUENCE free_fares_id_seq INCREMENT BY 1 MINVALUE 1 START 1;
CREATE TABLE free_fares (id INT NOT NULL default nextval('free_fares_id_seq'::regClass), conditions VARCHAR(255) NOT NULL, PRIMARY KEY(id));

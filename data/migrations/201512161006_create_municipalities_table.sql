CREATE SEQUENCE italian_municipalities_id_seq INCREMENT BY 1 MINVALUE 0 START 0;
CREATE TABLE italian_municipalities (id INT NOT NULL, cadastral_code VARCHAR(255) NOT NULL, province VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, foreign_name VARCHAR(255) DEFAULT NULL, istat_code VARCHAR(255) DEFAULT NULL, active BOOLEAN NOT NULL, PRIMARY KEY(id));
CREATE INDEX IDX_3396B6374ADAD40B ON italian_municipalities (province);
ALTER SEQUENCE italian_municipalities_id_seq OWNER TO sharengo;
ALTER TABLE italian_municipalities OWNER TO sharengo;
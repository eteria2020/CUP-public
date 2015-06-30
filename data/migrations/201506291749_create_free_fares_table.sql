CREATE SEQUENCE free_fares_id_seq INCREMENT BY 1 MINVALUE 1 START 1;
CREATE TABLE free_fares (id INT NOT NULL, conditions VARCHAR(255) NOT NULL, PRIMARY KEY(id));

INSERT INTO free_fares (conditions) VALUES
('{"customer":{"gender":"female"},"time":{"from":"00:00","to":"06:00"}}'),
('{"customer":{"birth_date":"today()"}}')
ALTER TABLE commands ADD COLUMN webuser_id integer;
ALTER TABLE commands ADD CONSTRAINT webuser_fk FOREIGN KEY (webuser_id) REFERENCES webuser(id);
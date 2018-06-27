ALTER TABLE business.transaction ALTER currency TYPE TEXT;
ALTER TABLE business.transaction ALTER outcome TYPE TEXT;

ALTER TABLE business.transaction ADD COLUMN contract_id int;
ALTER TABLE business.transaction ADD CONSTRAINT contract_fk FOREIGN KEY (contract_id) REFERENCES business.contract(id);
ALTER TABLE business.transaction ADD COLUMN first_transaction boolean;
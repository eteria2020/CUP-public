ALTER TABLE customers ADD CONSTRAINT card_code_fk FOREIGN KEY (card_code) REFERENCES cards (code);
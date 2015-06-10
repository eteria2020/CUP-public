ALTER TABLE customers ADD CONSTRAINT authority_fk FOREIGN KEY (driver_license_authority) REFERENCES authority (code);
ALTER TABLE customers ADD CONSTRAINT birth_province_fk FOREIGN KEY (birth_province) REFERENCES  provinces (code);


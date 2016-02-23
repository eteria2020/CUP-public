CREATE SEQUENCE foreign_drivers_license_upload_id_seq INCREMENT BY 1 MINVALUE 1 START 1;

CREATE TABLE foreign_drivers_license_upload (
    id INT NOT NULL,
    customer_id INT NOT NULL,
    customer_name VARCHAR(255) DEFAULT NULL,
    customer_surname VARCHAR(255) DEFAULT NULL,
    customer_birth_town VARCHAR(255) DEFAULT NULL,
    customer_birth_province VARCHAR(255) DEFAULT NULL,
    customer_birth_country VARCHAR(2) DEFAULT NULL,
    customer_birth_date DATE DEFAULT NULL,
    customer_country VARCHAR(2) DEFAULT NULL,
    customer_town VARCHAR(255) DEFAULT NULL,
    customer_address VARCHAR(255) DEFAULT NULL,
    drivers_license_number VARCHAR(255) DEFAULT NULL,
    drivers_license_authority VARCHAR(255) DEFAULT NULL,
    drivers_license_country VARCHAR(2) DEFAULT NULL,
    drivers_license_release_date DATE DEFAULT NULL,
    drivers_license_firstname VARCHAR(255) DEFAULT NULL,
    drivers_license_surname VARCHAR(255) DEFAULT NULL,
    drivers_license_categories VARCHAR(255) DEFAULT NULL,
    drivers_license_expire DATE DEFAULT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(255) DEFAULT NULL,
    file_location VARCHAR(255) DEFAULT NULL,
    file_size INT DEFAULT NULL,
    valid BOOLEAN DEFAULT NULL,
    validated_by INT DEFAULT NULL,
    validated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
    uploaded_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
    PRIMARY KEY(id)
);

CREATE INDEX IDX_E910C5F29395C3F3 ON foreign_drivers_license_upload (customer_id);
CREATE INDEX IDX_E910C5F2F54EF1C ON foreign_drivers_license_upload (validated_by);

ALTER TABLE foreign_drivers_license_upload
    ADD CONSTRAINT FK_E910C5F29395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE,
    ADD CONSTRAINT FK_E910C5F2F54EF1C FOREIGN KEY (validated_by) REFERENCES webuser (id) NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE foreign_drivers_license_upload OWNER TO sharengo;
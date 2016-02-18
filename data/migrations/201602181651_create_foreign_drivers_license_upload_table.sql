CREATE SEQUENCE foreign_drivers_license_upload_id_seq INCREMENT BY 1 MINVALUE 1 START 1;

CREATE TABLE foreign_drivers_license_upload (
    id INT NOT NULL,
    customer_id INT NOT NULL,
    customer_name VARCHAR(255) DEFAULT NULL,
    customer_surname VARCHAR(255) DEFAULT NULL,
    customer_birth_town VARCHAR(255) DEFAULT NULL,
    customer_birth_province VARCHAR(255) DEFAULT NULL,
    customer_birth_date DATE DEFAULT NULL,
    customer_country VARCHAR(2) DEFAULT NULL,
    customer_town VARCHAR(255) DEFAULT NULL,
    customer_address VARCHAR(255) DEFAULT NULL,
    drivers_license_number VARCHAR(255) DEFAULT NULL,
    driver_license_authority VARCHAR(255) DEFAULT NULL,
    driver_license_country VARCHAR(2) DEFAULT NULL,
    driver_license_release_date DATE DEFAULT NULL,
    driver_license_firstname VARCHAR(255) DEFAULT NULL,
    driver_license_surname VARCHAR(255) DEFAULT NULL,
    driver_license_categories VARCHAR(255) DEFAULT NULL,
    driver_license_expire DATE DEFAULT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(255) DEFAULT NULL,
    file_location VARCHAR(255) DEFAULT NULL,
    file_size INT DEFAULT NULL, PRIMARY KEY(id)
);

CREATE INDEX IDX_E910C5F29395C3F3 ON foreign_drivers_license_upload (customer_id);
ALTER TABLE foreign_drivers_license_upload
    ADD CONSTRAINT FK_E910C5F29395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE foreign_drivers_license_upload OWNER TO sharengo;
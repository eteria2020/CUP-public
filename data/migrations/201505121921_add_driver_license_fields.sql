ALTER TABLE customers ADD driver_license_authority VARCHAR(255) DEFAULT NULL;
ALTER TABLE customers ADD driver_license_country VARCHAR(2) DEFAULT NULL;
ALTER TABLE customers ADD driver_license_release_date DATE DEFAULT NULL;
ALTER TABLE customers ADD driver_license_name VARCHAR(255) DEFAULT NULL;
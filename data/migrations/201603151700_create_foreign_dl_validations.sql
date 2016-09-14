CREATE TABLE foreign_drivers_license_validation (
    id SERIAL PRIMARY KEY,
    foreign_drivers_license_upload_id INT REFERENCES foreign_drivers_license_upload(id),
    validated_by INT REFERENCES webuser(id),
    validated_at TIMESTAMP(0) WITHOUT TIME ZONE,
    revoked_by INT REFERENCES webuser(id),
    revoked_at TIMESTAMP(0) WITHOUT TIME ZONE
);


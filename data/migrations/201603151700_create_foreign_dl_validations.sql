CREATE TABLE foreign_drivers_license_validation (
    id SERIAL PRIMARY KEY,
    foreign_drivers_license_upload_id INT REFERENCES foreign_drivers_license_upload(id),
    validated_by INT REFERENCES webuser(id),
    validated_at TIMESTAMP(0) WITHOUT TIME ZONE,
    revoked_by INT REFERENCES webuser(id),
    revoked_at TIMESTAMP(0) WITHOUT TIME ZONE
);

ALTER TABLE foreign_drivers_license_validation OWNER TO sharengo;

INSERT INTO foreign_drivers_license_validation (foreign_drivers_license_upload_id, validated_by, validated_at)
    SELECT id, validated_by, validated_at
    FROM foreign_drivers_license_upload
    WHERE validated_by IS NOT NULL AND validated_at IS NOT NULL;

--remove migrated columns
ALTER TABLE foreign_drivers_license_upload
DROP COLUMN valid,
DROP COLUMN validated_by,
DROP COLUMN validated_at;
ALTER TABLE cars_maintenance
ADD end_webuser_id INT REFERENCES webuser (id) DEFAULT NULL;

ALTER TABLE cars_maintenance
ADD end_ts timestamp(0) DEFAULT NULL;

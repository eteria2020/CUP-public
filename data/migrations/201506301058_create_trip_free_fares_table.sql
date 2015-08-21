CREATE SEQUENCE trip_free_fares_id_seq INCREMENT BY 1 MINVALUE 1 START 1;
CREATE TABLE trip_free_fares (id INT NOT NULL, trip_id INT DEFAULT NULL, free_fare_id INT DEFAULT NULL, minutes INT NOT NULL, timestamp_beginning TIMESTAMP(0) WITH TIME ZONE NOT NULL, timestamp_end TIMESTAMP(0) WITH TIME ZONE NOT NULL, notes TEXT DEFAULT NULL, PRIMARY KEY(id));
CREATE INDEX IDX_BB724A6AA5BC2E0E ON trip_free_fares (trip_id);
CREATE INDEX IDX_BB724A6A8AEC039C ON trip_free_fares (free_fare_id);
ALTER TABLE trip_free_fares ADD CONSTRAINT FK_BB724A6AA5BC2E0E FOREIGN KEY (trip_id) REFERENCES trips (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE trip_free_fares ADD CONSTRAINT FK_BB724A6A8AEC039C FOREIGN KEY (free_fare_id) REFERENCES free_fares (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
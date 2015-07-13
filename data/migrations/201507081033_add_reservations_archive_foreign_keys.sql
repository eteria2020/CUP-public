ALTER TABLE reservations_archive ADD CONSTRAINT car_fk FOREIGN KEY (car_plate) REFERENCES cars (plate);
ALTER TABLE reservations_archive ADD CONSTRAINT customer_fk FOREIGN KEY (customer_id) REFERENCES customers (id);
CREATE INDEX IDX_4DA2399395C3F3 ON reservations_archive (customer_id);
CREATE INDEX IDX_4DA239AE35528C ON reservations_archive (car_plate);
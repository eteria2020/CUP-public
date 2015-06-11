ALTER TABLE reservations ADD CONSTRAINT car_fk FOREIGN KEY (car_plate) REFERENCES cars (plate);
ALTER TABLE reservations ADD CONSTRAINT customer_fk FOREIGN KEY (customer_id) REFERENCES customers (id);
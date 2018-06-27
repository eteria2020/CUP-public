ALTER TABLE safo_penalty DROP COLUMN penalty_id;



ALTER TABLE safo_penalty ALTER COLUMN customer_id DROP NOT NULL;

ALTER TABLE safo_penalty ALTER COLUMN trip_id DROP NOT NULL;

ALTER TABLE safo_penalty ALTER COLUMN vehicle_fleet_id DROP NOT NULL;

ALTER TABLE safo_penalty ALTER COLUMN car_plate DROP NOT NULL;



UPDATE safo_penalty set customer_id = NULL where customer_id = 0;

UPDATE safo_penalty set trip_id = NULL where trip_id = 0;

UPDATE safo_penalty set vehicle_fleet_id = NULL where vehicle_fleet_id = 0;



ALTER TABLE ONLY safo_penalty
ADD CONSTRAINT fk_customer_id FOREIGN KEY (customer_id) REFERENCES customers(id);

ALTER TABLE ONLY safo_penalty
ADD CONSTRAINT fk_trip_id FOREIGN KEY (trip_id) REFERENCES trips(id);

ALTER TABLE ONLY safo_penalty
ADD CONSTRAINT fk_car_plate FOREIGN KEY (car_plate) REFERENCES cars(plate);

ALTER TABLE ONLY safo_penalty
ADD CONSTRAINT fk_fleet_id FOREIGN KEY (vehicle_fleet_id) REFERENCES fleets(id);



ALTER TABLE safo_penalty ADD COLUMN extra_payment_id INTEGER DEFAULT NULL;

ALTER TABLE ONLY safo_penalty
    ADD CONSTRAINT fk_extra_payment_id FOREIGN KEY (extra_payment_id) REFERENCES extra_payments(id);

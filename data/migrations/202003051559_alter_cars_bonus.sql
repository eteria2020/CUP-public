ALTER TABLE cars_bonus ADD CONSTRAINT cars_bonus_cars FOREIGN KEY (car_plate) REFERENCES cars (plate) NOT DEFERRABLE INITIALLY IMMEDIATE;
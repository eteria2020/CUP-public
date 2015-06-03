DROP TYPE car_status CASCADE;

CREATE TYPE car_status AS ENUM ('operative', 'out_of_order', 'maintenance');

ALTER TABLE cars ADD status car_status DEFAULT 'maintenance';
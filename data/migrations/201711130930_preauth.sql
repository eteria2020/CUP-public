CREATE TYPE preauthorization_status AS ENUM ('to_be_payed', 'to_be_refund', 'wrong', 'done');

CREATE TABLE preauthorizations (
   id SERIAL PRIMARY KEY,
   customer_id int REFERENCES customers(id) NOT NULL,
   trip_id int REFERENCES trips(id),
   transaction_id int REFERENCES transactions(id),
   created_at timestamp(0) without time zone,
   status preauthorization_status DEFAULT NULL,
   status_from timestamp(0) without time zone DEFAULT NULL,
   successfully_at timestamp(0) without time zone DEFAULT NULL
);
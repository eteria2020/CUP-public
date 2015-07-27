CREATE TYPE invoice_type AS ENUM ('FIRST_PAYMENT', 'TRIP', 'PENALTY');

CREATE TABLE invoices (
    id SERIAL PRIMARY KEY,
    customer_id integer NOT NULL,
    generated_ts timestamp(0) with time zone NOT NULL,
    content jsonb NOT NULL, #text if psql 9.1,
    version int NOT NULL,
    type invoice_type NOT NULL,
    invoice_date integer NOT NULL,
    amount int NOT NULL,
    invoice_number varchar(15) NOT NULL
);

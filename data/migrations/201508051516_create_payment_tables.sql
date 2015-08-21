CREATE TYPE trip_payment_status AS ENUM ('not_payed', 'payed_correctly', 'wrong_payment', 'invoiced');

CREATE SEQUENCE trip_payments_id_seq INCREMENT BY 1 MINVALUE 1 START 1;
CREATE SEQUENCE trip_payment_tries_id_seq INCREMENT BY 1 MINVALUE 1 START 1;

CREATE TABLE trip_payments (
    id INT NOT NULL,
    trip_id INT NOT NULL,
    fare_id INT NOT NULL,
    invoice_id INT,
    trip_minutes INT NOT NULL,
    parking_minutes INT NOT NULL,
    discount_percentage INT NOT NULL,
    total_cost INT NOT NULL,
    status trip_payment_status DEFAULT 'not_payed' NOT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    payed_successfully_at TIMESTAMP(0) WITHOUT TIME ZONE,
    invoiced_at TIMESTAMP(0) WITHOUT TIME ZONE,
    PRIMARY KEY(id)
);

CREATE INDEX IDX_CD83A822A5BC2E0E ON trip_payments (trip_id);
CREATE INDEX IDX_CD83A822A048D2E2 ON trip_payments (fare_id);
CREATE INDEX IDX_CD83A8222989F1FD ON trip_payments (invoice_id);

CREATE TABLE trip_payment_tries (
    id INT NOT NULL,
    trip_payment_id INT NOT NULL,
    webuser_id INT,
    transaction_id INT,
    ts TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    outcome VARCHAR(255) NOT NULL,
    PRIMARY KEY(id)
);

CREATE INDEX IDX_435112D7F1D178AD ON trip_payment_tries (trip_payment_id);
CREATE INDEX IDX_435112D749279951 ON trip_payment_tries (webuser_id);
CREATE INDEX IDX_435112D72FC0CB0F ON trip_payment_tries (transaction_id);

ALTER TABLE trip_payments ADD CONSTRAINT FK_CD83A822A5BC2E0E FOREIGN KEY (trip_id) REFERENCES trips (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE trip_payments ADD CONSTRAINT FK_CD83A822A048D2E2 FOREIGN KEY (fare_id) REFERENCES fares (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE trip_payments ADD CONSTRAINT FK_CD83A8222989F1FD FOREIGN KEY (invoice_id) REFERENCES invoices (id) NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE trip_payment_tries ADD CONSTRAINT FK_435112D7F1D178AD FOREIGN KEY (trip_payment_id) REFERENCES trip_payments (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE trip_payment_tries ADD CONSTRAINT FK_435112D749279951 FOREIGN KEY (webuser_id) REFERENCES webuser (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE trip_payment_tries ADD CONSTRAINT FK_435112D72FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transactions (id) NOT DEFERRABLE INITIALLY IMMEDIATE;

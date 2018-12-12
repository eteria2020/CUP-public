CREATE TABLE extra_payment_rates ( 
    id integer NOT NULL,
    customer_id integer NOT NULL,
    amount integer NOT NULL,
    insert_ts timestamp(0) without time zone NOT NULL,
    debit_ts timestamp(0) without time zone NOT NULL,
    extra_payment_father_id integer NOT NULL,
    extra_payment_id integer,
    payable boolean NOT NULL
);

ALTER TABLE extra_payment_rates OWNER TO sharengo;


CREATE SEQUENCE extra_payment_rates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE extra_payment_rates_id_seq OWNER TO sharengo;


ALTER SEQUENCE extra_payment_rates_id_seq OWNED BY extra_payment_rates.id;

ALTER TABLE ONLY extra_payment_rates ALTER COLUMN id SET DEFAULT nextval('maintenance_locations_id_seq'::regclass);

ALTER TABLE ONLY extra_payment_rates
    ADD CONSTRAINT extra_payment_rates_pkey PRIMARY KEY (id);

ALTER TABLE ONLY extra_payment_rates
    ADD CONSTRAINT fk_extra_payment_rates_customers FOREIGN KEY (customer_id) REFERENCES customers(id)
	
ALTER TABLE ONLY extra_payment_rates
    ADD CONSTRAINT fk_extra_payment_rates_extra_payment FOREIGN KEY (extra_payment_id) REFERENCES extra_payments(id);

ALTER TABLE ONLY extra_payment_rates
    ADD CONSTRAINT fk_extra_payment_rates_father_extra_payment FOREIGN KEY (extra_payment_father_id) REFERENCES extra_payments(id);
	
	
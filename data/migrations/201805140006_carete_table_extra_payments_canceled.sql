-- Name: extra_payments_canceled; Type: TABLE; Schema: public; Owner: sharengo; Tablespace: 
--

CREATE TABLE extra_payments_canceled (
    id integer NOT NULL,
    inserted_ts timestamp(0) without time zone NOT NULL,
    webuser_id integer NOT NULL,
	customer_id integer NOT NULL,
	amount integer NOT NULL,
	fleet_id integer DEFAULT 1 NOT NULL,
	generated_ts timestamp(0) without time zone NOT NULL,
    transaction_id integer,
    reasons jsonb NOT NULL,
    payment_type extra_payments_types NOT NULL,
	first_extra_try_ts timestamp(0) without time zone
);


ALTER TABLE extra_payments_canceled OWNER TO sharengo;

--
-- Name: extra_payments_canceled_id_seq; Type: SEQUENCE; Schema: public; Owner: sharengo
--

CREATE SEQUENCE extra_payments_canceled_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE extra_payments_canceled_id_seq OWNER TO sharengo;

--
-- Name: extra_payments_canceled_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sharengo
--

ALTER SEQUENCE extra_payments_canceled_id_seq OWNED BY extra_payments_canceled.id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: sharengo
--

ALTER TABLE ONLY extra_payments_canceled ALTER COLUMN id SET DEFAULT nextval('extra_payments_canceled_id_seq'::regclass);


--
-- Name: extra_script_runs_pkey; Type: CONSTRAINT; Schema: public; Owner: sharengo; Tablespace: 
--

ALTER TABLE ONLY extra_payments_canceled
    ADD CONSTRAINT extra_payments_canceled_pkey PRIMARY KEY (id);

----------------------------------------------------------------------------------------------
--
-- Name: fk_extra_payment_canceled_customer_id; Type: FK CONSTRAINT; Schema: public; Owner: sharengo
--

ALTER TABLE ONLY extra_payments_canceled
    ADD CONSTRAINT fk_extra_payment_canceled_customer_id FOREIGN KEY (customer_id) REFERENCES customers(id);


--
-- Name: fk_extra_payment_canceled_fleet_id; Type: FK CONSTRAINT; Schema: public; Owner: sharengo
--

ALTER TABLE ONLY extra_payments_canceled
    ADD CONSTRAINT fk_extra_payment_canceled_fleet_id FOREIGN KEY (fleet_id) REFERENCES fleets(id);


--
-- Name: fk_extra_payment_canceled_webuser_id; Type: FK CONSTRAINT; Schema: public; Owner: sharengo
--

ALTER TABLE ONLY extra_payments_canceled
ADD CONSTRAINT fk_extra_payment_canceled_webuser_id FOREIGN KEY (webuser_id) REFERENCES webuser(id);


--
-- Name: fk_extra_payment_canceled_transaction_id; Type: FK CONSTRAINT; Schema: public; Owner: sharengo
--

ALTER TABLE ONLY extra_payments_canceled
    ADD CONSTRAINT fk_extra_payment_canceled_transaction_id FOREIGN KEY (transaction_id) REFERENCES transactions(id);
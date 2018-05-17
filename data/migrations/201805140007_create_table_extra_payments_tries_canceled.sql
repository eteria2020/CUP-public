CREATE TABLE extra_payment_tries_canceled (
    id integer NOT NULL,
	inserted_ts timestamp(0) without time zone NOT NULL,
    extra_payment_canceled_id integer NOT NULL,
    webuser_id integer,
    transaction_id integer,
    ts timestamp(0) without time zone NOT NULL,
    outcome character varying(255) NOT NULL
);

-----------------------------------------------------------------

ALTER TABLE extra_payment_tries_canceled OWNER TO sharengo;


---------------------------------------------------------------

--
-- Name: extra_payment_tries_canceled; Type: SEQUENCE; Schema: public; Owner: sharengo
--

CREATE SEQUENCE extra_payment_tries_canceled_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE extra_payment_tries_canceled_id_seq OWNER TO sharengo;

--
-- Name: extra_payment_tries_canceled_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sharengo
--

ALTER SEQUENCE extra_payment_tries_canceled_id_seq OWNED BY extra_payment_tries_canceled.id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: sharengo
--

ALTER TABLE ONLY extra_payment_tries_canceled ALTER COLUMN id SET DEFAULT nextval('extra_payment_tries_canceled_id_seq'::regclass);

-----------------------------------------------------------------

ALTER TABLE ONLY extra_payment_tries_canceled
ADD CONSTRAINT extra_payment_tries_canceled_pkey PRIMARY KEY (id);

-----------------------------------------------------------------

ALTER TABLE ONLY extra_payment_tries_canceled
ADD CONSTRAINT fk_payment_tries_canceled_transaction_id FOREIGN KEY (transaction_id) REFERENCES transactions(id);
	
ALTER TABLE ONLY extra_payment_tries_canceled
ADD CONSTRAINT fk__payment_tries_canceled_webuser_id FOREIGN KEY (webuser_id) REFERENCES webuser(id);
	
ALTER TABLE ONLY extra_payment_tries_canceled
ADD CONSTRAINT fk_extra_payment_tries_canceled_extra_payment_id FOREIGN KEY (extra_payment_canceled_id) REFERENCES extra_payments(id);
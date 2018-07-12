CREATE TABLE safo_penalty (
  id integer NOT NULL,
  penalty_id integer,
  insert_ts timestamp with time zone NOT NULL,
  charged boolean NOT NULL,
  consumed_ts timestamp with time zone,
  customer_id integer NOT NULL,
  vehicle_fleet_id integer NOT NULL,
  violation_category integer NOT NULL,
  trip_id integer NOT NULL,
  car_plate text,
  violation_timestamp timestamp with time zone NOT NULL,
  violation_authority text NOT NULL,
  violation_number text NOT NULL,
  violation_description text NOT NULL,
  rus_id integer NOT NULL,
  violation_request_type integer NOT NULL,
  violation_status character(1) NOT NULL,
  email_sent_timestamp timestamp with time zone,
  email_sent_ok boolean,
  penalty_ok boolean,
  amount integer DEFAULT 0 NOT NULL,
  complete boolean DEFAULT false NOT NULL
);


ALTER TABLE safo_penalty OWNER TO postgres;

--
-- Name: safo_penalty_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE safo_penalty_id_seq
  START WITH 1
  INCREMENT BY 1
  NO MINVALUE
  NO MAXVALUE
  CACHE 1;


ALTER TABLE safo_penalty_id_seq OWNER TO postgres;

--
-- Name: safo_penalty_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE safo_penalty_id_seq OWNED BY safo_penalty.id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY safo_penalty ALTER COLUMN id SET DEFAULT nextval('safo_penalty_id_seq'::regclass);


--
-- Name: safo_penalty_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY safo_penalty
  ADD CONSTRAINT safo_penalty_pkey PRIMARY KEY (id);
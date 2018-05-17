--
-- Name: extra_script_runs; Type: TABLE; Schema: public; Owner: sharengo; Tablespace: 
--

CREATE TABLE extra_script_runs (
    id integer NOT NULL,
    start_ts timestamp(0) without time zone NOT NULL,
    end_ts timestamp(0) without time zone
);


ALTER TABLE extra_script_runs OWNER TO sharengo;

--
-- Name: extra_script_runs_id_seq; Type: SEQUENCE; Schema: public; Owner: sharengo
--

CREATE SEQUENCE extra_script_runs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE extra_script_runs_id_seq OWNER TO sharengo;

--
-- Name: extra_script_runs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sharengo
--

ALTER SEQUENCE extra_script_runs_id_seq OWNED BY extra_script_runs.id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: sharengo
--

ALTER TABLE ONLY extra_script_runs ALTER COLUMN id SET DEFAULT nextval('extra_script_runs_id_seq'::regclass);


--
-- Name: extra_script_runs_pkey; Type: CONSTRAINT; Schema: public; Owner: sharengo; Tablespace: 
--

ALTER TABLE ONLY extra_script_runs
    ADD CONSTRAINT extra_script_runs_pkey PRIMARY KEY (id);


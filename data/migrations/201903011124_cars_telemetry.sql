CREATE OR REPLACE FUNCTION "public"."update_car_last_contact" () RETURNS trigger AS 'DECLARE
BEGIN
  UPDATE cars SET last_contact = now() WHERE plate = new.car_plate;

  RETURN NEW;
END;' LANGUAGE "plpgsql" COST 100
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER;


--
-- Name: cars_telemetry; Type: TABLE; Schema: public; Owner: sharengo; Tablespace: 
--

CREATE TABLE public.cars_telemetry (
    car_plate text NOT NULL,
    latitude numeric DEFAULT 0.0,
    longitude numeric DEFAULT 0.0,
    last_location_update timestamp with time zone DEFAULT now() NOT NULL,
    imei text DEFAULT '000000000000'::text NOT NULL,
    vip text DEFAULT '000000000000'::text NOT NULL,
    iccid text,
    soc numeric DEFAULT 0,
    v_batt numeric DEFAULT 0,
    a_batt numeric DEFAULT 0,
    plug boolean DEFAULT false,
    v_cells jsonb DEFAULT '{}'::jsonb,
    sim_iccid text
);


ALTER TABLE public.cars_telemetry OWNER TO sharengo;

--
-- Name: TABLE cars_telemetry; Type: COMMENT; Schema: public; Owner: sharengo
--

COMMENT ON TABLE public.cars_telemetry IS 'cars telemetry received from GPRS';


--
-- Name: cars_telemetry_pkey; Type: CONSTRAINT; Schema: public; Owner: sharengo; Tablespace: 
--

ALTER TABLE ONLY public.cars_telemetry
    ADD CONSTRAINT cars_telemetry_pkey PRIMARY KEY (car_plate);


--
-- Name: car_last_contact; Type: TRIGGER; Schema: public; Owner: sharengo
--

CREATE TRIGGER car_last_contact AFTER UPDATE ON public.cars_telemetry FOR EACH ROW EXECUTE PROCEDURE public.update_car_last_contact();


--
-- Name: cars_fk; Type: FK CONSTRAINT; Schema: public; Owner: sharengo
--

ALTER TABLE ONLY public.cars_telemetry
    ADD CONSTRAINT cars_fk FOREIGN KEY (car_plate) REFERENCES public.cars(plate) ON UPDATE CASCADE ON DELETE CASCADE;

	
CREATE OR REPLACE FUNCTION "public"."insert_car_telemetry" () RETURNS trigger AS 'BEGIN
	INSERT INTO cars_telemetry (car_plate) VALUES (NEW.plate);
	RETURN NEW;
END;	' LANGUAGE "plpgsql" COST 100
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER;

CREATE TRIGGER create_car_telemetry AFTER INSERT ON public.cars FOR EACH ROW EXECUTE PROCEDURE insert_car_telemetry();
INSERT INTO cars_telemetry(car_plate)(SELECT plate from cars);
--
-- PostgreSQL database dump complete
--

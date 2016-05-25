
CREATE TABLE cars_info (
    car_plate text NOT NULL,
    int_lat double precision,
    int_lon double precision,
    int_geo geometry,
    gprs_lat double precision,
    gprs_lon double precision,
    gprs_geo geometry,
    fw_ver text,
    hw_ver text,
    sw_ver text,
    sdk text,
    sdk_ver text,
    gsm_ver text,
    android_device text,
    android_build text,
    tbox_sw text,
    tbox_hw text,
    mcu_model text,
    mcu text,
    hw_version text,
    hb_ver text,
    vehicle_type text,
    lastupdate timestamp with time zone,
    gps text
);

--
-- Name: COLUMN cars_info.fw_ver; Type: COMMENT; Schema: public; Owner: cs
--

COMMENT ON COLUMN cars_info.fw_ver IS 'Versione Firmware GPRS Box';


--
-- Name: COLUMN cars_info.hw_ver; Type: COMMENT; Schema: public; Owner: cs
--

COMMENT ON COLUMN cars_info.hw_ver IS 'Versione Hardware Android';


--
-- Name: COLUMN cars_info.sw_ver; Type: COMMENT; Schema: public; Owner: cs
--

COMMENT ON COLUMN cars_info.sw_ver IS 'Versione OBC';


--
-- Name: COLUMN cars_info.sdk; Type: COMMENT; Schema: public; Owner: cs
--

COMMENT ON COLUMN cars_info.sdk IS 'Versione Servizio Android HIKSDK';


--
-- Name: COLUMN cars_info.gsm_ver; Type: COMMENT; Schema: public; Owner: cs
--

COMMENT ON COLUMN cars_info.gsm_ver IS 'Versione Modulo 3G';


--
-- Name: COLUMN cars_info.android_device; Type: COMMENT; Schema: public; Owner: cs
--

COMMENT ON COLUMN cars_info.android_device IS 'Modello Android (rilevato dal S.O. Android)';


--
-- Name: COLUMN cars_info.android_build; Type: COMMENT; Schema: public; Owner: cs
--

COMMENT ON COLUMN cars_info.android_build IS 'Versione O.S.';


--
-- Name: COLUMN cars_info.tbox_sw; Type: COMMENT; Schema: public; Owner: cs
--

COMMENT ON COLUMN cars_info.tbox_sw IS 'Versione Firmware Box GPRS';


--
-- Name: COLUMN cars_info.tbox_hw; Type: COMMENT; Schema: public; Owner: cs
--

COMMENT ON COLUMN cars_info.tbox_hw IS 'Versione Hardware Box GPRS';


--
-- Name: COLUMN cars_info.mcu_model; Type: COMMENT; Schema: public; Owner: cs
--

COMMENT ON COLUMN cars_info.mcu_model IS 'Modello MCU';


--
-- Name: COLUMN cars_info.mcu; Type: COMMENT; Schema: public; Owner: cs
--

COMMENT ON COLUMN cars_info.mcu IS 'Versione Software MCU';


--
-- Name: COLUMN cars_info.vehicle_type; Type: COMMENT; Schema: public; Owner: cs
--

COMMENT ON COLUMN cars_info.vehicle_type IS 'Modello Automobile';


--
-- Name: COLUMN cars_info.gps; Type: COMMENT; Schema: public; Owner: cs
--

COMMENT ON COLUMN cars_info.gps IS 'Fonte Coordinate GPS ( INT=Android | EXT=GPRS Box )';

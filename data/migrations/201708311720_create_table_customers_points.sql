CREATE TABLE customers_points (
    id integer NOT NULL,
    customer_id integer NOT NULL,
    webuser_id integer,
    active boolean NOT NULL,
    insert_ts timestamp(0) without time zone NOT NULL,
    update_ts timestamp(0) without time zone NOT NULL,
    total integer NOT NULL,
    residual integer NOT NULL,
    type character varying(100) NOT NULL,
    valid_from timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    duration_days integer,
    valid_to timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    description text,
);


--ADD PRIMARY KEY to customers_points
ALTER TABLE ONLY customers_points
ADD CONSTRAINT customers_points_pkey PRIMARY KEY (id);
	
	
	
--ADD FOREIGN KEY to customers_points REFERENCES customers(id)
ALTER TABLE ONLY customers_points
ADD CONSTRAINT customer_id FOREIGN KEY (customer_id) REFERENCES customers(id)

--ADD FOREIGN KEY to customers_points REFERENCES webuser(id)
ALTER TABLE ONLY customers_points
ADD CONSTRAINT webuser_id FOREIGN KEY (webuser_id) REFERENCES webuser(id);

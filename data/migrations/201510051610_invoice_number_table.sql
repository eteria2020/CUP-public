CREATE TABLE invoice_number
(
  id serial NOT NULL,
  year integer NOT NULL,
  fleet_id integer NOT NULL,
  "number" integer NOT NULL,
  CONSTRAINT pk PRIMARY KEY (id)
);

ALTER TABLE invoice_number
  OWNER TO sharengo;

/* change 4310 to match the last invoice number value */
INSERT INTO invoice_number (year, fleet_id, number) VALUES
(2015, 1, 4310);
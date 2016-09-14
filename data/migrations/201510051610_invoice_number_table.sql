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


CREATE TABLE public.cards
(
  rfid text NOT NULL,
  code text NOT NULL,
  is_assigned boolean NOT NULL DEFAULT false,
  notes text,
  CONSTRAINT cards_pkey PRIMARY KEY (rfid),
  CONSTRAINT cards_code_key UNIQUE (code)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE cards
  OWNER TO  sharengo;
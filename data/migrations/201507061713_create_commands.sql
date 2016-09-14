CREATE TABLE commands
(
  id bigserial NOT NULL,
  car_plate text,
  command text,
  intarg1 integer DEFAULT 0,
  intarg2 integer DEFAULT 0,
  txtarg1 text,
  txtarg2 text,
  queued timestamp with time zone,
  to_send boolean DEFAULT false,
  received timestamp with time zone,
  ttl integer DEFAULT 0,
  payload jsonb,
  CONSTRAINT commands_pk PRIMARY KEY (id)
)


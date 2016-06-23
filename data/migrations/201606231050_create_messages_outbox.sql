CREATE TABLE messages_outbox
(
  id serial NOT NULL,
  transport text,
  destination text,
  type text,
  subject text,
  text text,
  submitted timestamp with time zone,
  sent timestamp with time zone,
  acknowledged timestamp with time zone,
  meta jsonb,
  CONSTRAINT messages_outbox_pk PRIMARY KEY (id)
)

WITH (
  OIDS=FALSE
);

ALTER TABLE messages_outbox OWNER TO cs;
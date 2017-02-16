CREATE TABLE messages_transports
(
  name text NOT NULL,
  description text,
  CONSTRAINT messages_transports_pk PRIMARY KEY (name)
);

ALTER TABLE messages_transports OWNER TO sharengo;

CREATE TABLE messages_types
(
  name text NOT NULL,
  description text,
  default_transport text,
  CONSTRAINT messages_types_pk PRIMARY KEY (name),
  CONSTRAINT default_transport_fk FOREIGN KEY (default_transport)
      REFERENCES messages_transports (name) MATCH FULL
      ON UPDATE NO ACTION ON DELETE NO ACTION
);

ALTER TABLE messages_types OWNER TO sharengo;

COMMENT ON COLUMN messages_types.default_transport IS 'The default transport to use to send this message type.
Can be overrided by any "messages_transports" records, specifing the transport in the homonym field.';

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
  sent_meta jsonb,
  CONSTRAINT messages_outbox_pk PRIMARY KEY (id),
  CONSTRAINT transport_fk FOREIGN KEY (transport)
      REFERENCES messages_transports (name) MATCH FULL
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT type_fk FOREIGN KEY (type)
      REFERENCES messages_types (name) MATCH FULL
      ON UPDATE NO ACTION ON DELETE NO ACTION
);

ALTER TABLE messages_outbox OWNER TO sharengo;

COMMENT ON COLUMN messages_outbox.sent_meta IS 'This field contains send information in JSON type.
Shoud countain for example send errors. ';

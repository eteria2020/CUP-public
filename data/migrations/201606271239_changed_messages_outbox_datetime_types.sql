ALTER TABLE messages_outbox
    ALTER COLUMN submitted TYPE timestamp(0) with time zone,
    ALTER COLUMN sent TYPE timestamp(0) with time zone,
    ALTER COLUMN acknowledged TYPE timestamp(0) with time zone;
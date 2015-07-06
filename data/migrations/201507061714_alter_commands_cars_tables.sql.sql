ALTER TABLE commands RENAME comand  TO command;

ALTER TABLE cars ADD COLUMN charging boolean NOT NULL DEFAULT false;
ALTER TABLE cars ADD COLUMN battery_offset integer NOT NULL DEFAULT 0;
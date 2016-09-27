CREATE SEQUENCE zone_bonus_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE zone_bonus (
    id integer PRIMARY KEY NOT NULL,
    geo polygon NOT NULL,
    active boolean NOT NULL,
    description text,
    bonus_type text NOT NULL,
    conditions text
);

-- Se serve:
-- ALTER TABLE zone_bonus ADD CONSTRAINT zone_bonus_id primary KEY (id)
-- ALTER TABLE zone_bonus ADD COLUMN fleets_id integer[] not null default '{}';
-- alter table zone_bonus add column geo polygon not null default '((0.0,0.0))'

CREATE TABLE zone_bonus_fleets (zone_bonus_id INT NOT NULL, fleet_id INT NOT NULL, PRIMARY KEY(fleet_id, zone_bonus_id));
CREATE INDEX IDX_66DC9D244B061DF9 ON zone_bonus_fleets (fleet_id);
CREATE INDEX IDX_66DC9D2441E8DAFB ON zone_bonus_fleets (zone_bonus_id);
ALTER TABLE zone_bonus_fleets ADD CONSTRAINT FK_66DC9D244B061DF9 FOREIGN KEY (fleet_id) REFERENCES fleets (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE zone_bonus_fleets ADD CONSTRAINT FK_66DC9D2441E8DAFB FOREIGN KEY (zone_bonus_id) REFERENCES zone_bonus (id) NOT DEFERRABLE INITIALLY IMMEDIATE;

# Poi

ALTER TABLE trips
ADD COLUMN bonus_computed BOOLEAN NOT NULL DEFAULT FALSE;

UPDATE trips SET bonus_computed = true;

# Oppure (meglio)

ALTER TABLE trips
ADD COLUMN bonus_computed BOOLEAN NOT NULL DEFAULT TRUE;

alter table trips alter bonus_computed set default false;


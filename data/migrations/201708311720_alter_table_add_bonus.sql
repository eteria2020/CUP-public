--CREATE COLUMN IN add_bonus
ALTER TABLE add_bonus ADD COLUMN type character varying(50);


--UPDATE COLUMN TYPE with type='min'
UPDATE add_bonus
SET type = 'min'



--ALTER COLUMN TYPE from NULL to NOT NULL
ALTER TABLE add_bonus ALTER COLUMN type character varying(50) NOT NULL;
ALTER TABLE free_fares
ADD active boolean NOT NULL DEFAULT true;

UPDATE free_fares
SET active = false
WHERE conditions = '{"customer":{"birth_date":"today()"}}';

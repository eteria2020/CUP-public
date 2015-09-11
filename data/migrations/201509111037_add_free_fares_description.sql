ALTER TABLE free_fares ADD COLUMN description varchar(255);
UPDATE free_fares SET description = 'Compleanno' WHERE id = 2;
UPDATE free_fares SET description = 'Gratuità donne 01:00-06:00' WHERE id = 1;
ALTER TABLE free_fares ALTER COLUMN description SET NOT NULL;

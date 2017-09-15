INSERT INTO add_bonus (id, description, type)
VALUES ((select max(id)+1 from add_bonus), 'Aggiunta generica di punti', 'point');
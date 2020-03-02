UPDATE customers_bonus_packages SET cost=990 ,minutes=45    WHERE id=1 and description='Uvítací balík';
UPDATE customers_bonus_packages SET cost=1990 ,minutes=77   WHERE id=2 and description='Fast Ride';
UPDATE customers_bonus_packages SET cost=3990 ,minutes=190  WHERE id=3 and description='Smart';
UPDATE customers_bonus_packages SET cost=5990 ,minutes=315  WHERE id=4 and description='Best Rider';
UPDATE customers_bonus_packages SET cost=5000 ,minutes=1440 WHERE id=7 and description='Balík denných minút';
UPDATE customers_bonus_packages SET cost=9000 ,minutes=2880 WHERE id=8 and description='Balenie 48 hodín';

INSERT INTO customers_bonus_packages ( id, code, inserted_ts, minutes, type, valid_from, duration, valid_to, buyable_until, description, cost, notes, name, display_priority)
VALUES (9, 'GOLDEN', now(), 43200, 'Pacchetto', now(), 30,NULL, '2021-05-01 00:00:00','Jazdi koľko chceš! Pre tých, ktorí sa nechcú obmedzovať. Jazdi 30 dní bez obmedzení. Platnosť balíčka je 30 dní od zakúpenia.',49900,'','ZLATÝ PAUŠÁL',10);

UPDATE fares SET motion_cost_per_minute=29, park_cost_per_minute=29 WHERE id=1;

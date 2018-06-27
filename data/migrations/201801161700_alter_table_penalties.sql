ALTER TABLE penalties ADD COLUMN type varchar(10);

update penalties  set type = 'penalties';

ALTER TABLE penalties ALTER COLUMN type SET NOT NULL;

Insert into penalties
Values
((select max(id)+1 from penalties) , 'Addebito corse', null, 'extra'),
((select max(id)+2 from penalties) , 'Addebito rimozionee', null, 'extra'),
((select max(id)+3 from penalties) , 'Pacchetto My Sharengo', null, 'extra'),
((select max(id)+4 from penalties) , 'Addebito franchigia sinistro stradale', null, 'extra'),
((select max(id)+5 from penalties) , 'Addebito parcheggi', null, 'extra'),
((select max(id)+6 from penalties) , 'Acquisto pacchetto', null, 'extra')


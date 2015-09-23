CREATE SEQUENCE penalties_id_seq INCREMENT BY 1 MINVALUE 1 START 1;

CREATE TABLE penalties (id INT NOT NULL, reason VARCHAR(255) NOT NULL, amount INT DEFAULT NULL, PRIMARY KEY(id));

INSERT INTO penalties VALUES
(nextval('penalties_id_seq'), 'Notifica sanzioni e multe', 2000),
(nextval('penalties_id_seq'), 'Gestione pratiche rimozione veicolo o danni autoinflitti', 5000),
(nextval('penalties_id_seq'), 'Riattivazione del servizio, occorsa per sospensione patente, sospensione per mancati pagamenti, etc ', 2000),
(nextval('penalties_id_seq'), 'Pulizia straordinaria', 5000),
(nextval('penalties_id_seq'), 'Sanificazione dovuta a trasporto animali', 15000),
(nextval('penalties_id_seq'), 'Fumare all''interno del veicolo', 5000),
(nextval('penalties_id_seq'), 'Rilascio del veicolo con luci accese o finestrini abbassati', 5000),
(nextval('penalties_id_seq'), 'Rimozione del veicolo in caso di parcheggio in divieto di sosta o in area privata', 10000),
(nextval('penalties_id_seq'), 'Rimozione forzata del veicolo a seguito di una infrazione', 10000),
(nextval('penalties_id_seq'), 'Rilascio del veicolo senza aver terminato correttamente la procedura', 5000),
(nextval('penalties_id_seq'), 'Soccorso stradale perch&egrave; il Cliente non avendo osservato il segnale di riserva ha lasciato il veicolo con carica/autonomia inferiore al 10%', 12000),
(nextval('penalties_id_seq'), 'Soccorso stradale per danni causati dal Cliente, con o senza controparte (CID passivo)', 10000),
(nextval('penalties_id_seq'), 'Smarrimento o danneggiamento dei documenti del veicolo', 5000),
(nextval('penalties_id_seq'), 'Smarrimento cavo elettrico di emergenza sito nel baule', 5000),
(nextval('penalties_id_seq'), 'Smarrimento Kit di emergenza sito nel baule', 5000),
(nextval('penalties_id_seq'), 'Mancato rispetto delle istruzioni ricevute dal servizio clienti SHAREN''GO o dall''operatore intervenuto sul posto in caso di guasto o incidente', 5000),
(nextval('penalties_id_seq'), 'Estrazione o smarrimento della chiave di accensione', 25000),
(nextval('penalties_id_seq'), 'Guida all''estero', 25000),
(nextval('penalties_id_seq'), 'Guida del veicolo da parte di soggetto diverso da quello che ha effettuato la prenotazione', 10000),
(nextval('penalties_id_seq'), 'Gestione sinistri non comunicati dal Cliente', 10000),
(nextval('penalties_id_seq'), 'Recupero del veicolo fuori dall''area di copertura della citt&agrave; per responsabilit&agrave; del Cliente', NULL),
(nextval('penalties_id_seq'), 'Mancata pronta restituzione a seguito di richiesta del servizio clienti SHAREN''GO', NULL),
(nextval('penalties_id_seq'), 'Dichiarazioni verificatesi inequivocabilmente false in fase di profilazione-registrazione', NULL),
(nextval('penalties_id_seq'), 'Utilizzazione notturna da parte di un utente di sesso maschile di un auto a tariffa gratuita utilizzando i codici d''uso e accesso di un utente di sesso femminile', NULL),
(nextval('penalties_id_seq'), 'Affidamento del veicolo a minore anche se in possesso di patente B1', NULL);
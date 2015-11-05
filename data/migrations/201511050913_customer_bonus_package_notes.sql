ALTER TABLE customers_bonus_packages ADD notes TEXT DEFAULT NULL;

/* here I'm supposing there is only one bonus package */
UPDATE customers_bonus_packages SET notes = 'Acquistando questo pacchetto potrai guidare le equomobili di Sharen`go per soli 6€ / ora (0,10€ / minuto). I tuoi 1.000 minuti saranno aggiunti ai tuoi minuti bonus e potrai utilizzarli nei successivi 90 giorni'
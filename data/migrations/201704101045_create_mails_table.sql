CREATE TABLE mails (
    id SERIAL PRIMARY KEY,
    subject text NOT NULL,
	content text NOT NULL,
	enable boolean default TRUE,
    language varchar(10) NOT NULL,
	category int NOT NULL
);
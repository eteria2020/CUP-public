CREATE TABLE authority (
    code varchar(3) PRIMARY KEY,
    name text NOT NULL
);

INSERT INTO authority (code, name) VALUES
('DTT', 'Dipartimento dei Trasporti Terrestri'),
('MC', 'Motorizzazione Civile'),
('C', 'Comune'),
('AE', 'Altro ente');
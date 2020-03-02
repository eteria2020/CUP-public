COMMENT ON COLUMN customers.general_condition1  IS 'Clausole assicurative. Opzionale. Autorizzo il trattamento dei miei dati personali per finalità assicurative, antifrode e prevenzione sinistri (facoltativo).';
COMMENT ON COLUMN customers.general_condition2  IS 'Clausole vessatorie. Obbligatorio.  Il Cliente, dopo aver preso visione dei Termini ...';
COMMENT ON COLUMN customers.privacy_condition  IS 'Privacy. Obbligatorio. Accetto espressamente i Termini e le Condizioni Privacy  ...';
COMMENT ON COLUMN customers.privacy_information IS 'Campo obsoleto';
COMMENT ON COLUMN customers.newsletter  IS 'News letter. Opzionale. Accetto di ricevere comunicazioni cartacee e  ...';

COMMENT ON COLUMN customers.regulation_condition1 IS 'Campo obsoleto';
COMMENT ON COLUMN customers.regulation_condition2 IS 'Campo obsoleto';

-- UPDATE customers SET general_condition1=false, general_condition2=true, newsletter=true, privacy_condition=true;

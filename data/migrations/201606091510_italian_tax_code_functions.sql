-- Function: public.cf_calcolo_nome(nome)

-- DROP FUNCTION public.cf_calcolo_nome(text);

CREATE OR REPLACE FUNCTION public.cf_calcolo_nome(nome text)
RETURNS text AS
$BODY$
	DECLARE
        i integer;
        y integer;
        n_ascii integer;
        v_cara char(1);
        v_vocali text;
        v_consonanti varchar(4);
        v_cod_fis text;
	BEGIN
        i := 0;
        y := 0; 

        while i < length(nome) and y <> 4 loop
            v_cara  := substr(upper(nome), i+1, 1); -- estraggo il carattere
            n_ascii := ascii(v_cara); -- estraggo il codice ascii
            -- controllo se il codice ascii appartiene al range di valori accettati compreso fra 65 e 90
            if(n_ascii > 64 and n_ascii < 91) then
                -- controllo se il codice ascii è una vocale
                if(
                    n_ascii = 65 or
                    n_ascii = 69 or
                    n_ascii = 73 or
                    n_ascii = 79 or
                    n_ascii = 85 ) then
                        v_vocali := concat(v_vocali,v_cara);
                    else
                        v_consonanti := concat(v_consonanti,v_cara);
                        y := y + 1;
                end if;
            end if;
            i := i + 1;
        end loop;

        -- assegno il valore del codice fiscale
        IF( length(v_consonanti)>3 ) THEN
            v_cod_fis := substr(upper(v_consonanti), 1, 1);
            v_cod_fis := concat(v_cod_fis,substr(upper(v_consonanti), 3, 2));
        ELSE
            v_cod_fis := substr(concat(v_consonanti,v_vocali), 1, 3);
            while length(v_cod_fis) < 3 loop
                v_cod_fis := concat(v_cod_fis,'X');
            end loop;
        END IF;

    RETURN(v_cod_fis);

    EXCEPTION WHEN OTHERS THEN RETURN('CF-002');
END;
$BODY$
LANGUAGE plpgsql STABLE STRICT
COST 100;

ALTER FUNCTION public.cf_calcolo_nome(text) OWNER TO sharengo;

-- Function: public.cf_calcolo_cognome(cognome)

-- DROP FUNCTION public.cf_calcolo_cognome(text);

CREATE OR REPLACE FUNCTION public.cf_calcolo_cognome(cognome text)
RETURNS text AS
$BODY$
	DECLARE
        i integer;
        y integer;
        z integer;
        n_ascii integer;
        v_cara char(1);
        v_vocali text;
        v_consonanti varchar(3);
        v_cod_fis text;
	BEGIN
        i := 0;
        y := 0; 
        z := 0;

        while i < length(cognome) and y <> 3 loop
            v_cara  := substr(upper(cognome), i+1, 1); -- estraggo il carattere
            n_ascii := ascii(v_cara); -- estraggo il codice ascii
            -- controllo se il codice ascii appartiene al range di valori accettati compreso fra 65 e 90
            if(n_ascii > 64 and n_ascii < 91) then
                -- controllo se il codice ascii è una vocale
                if(
                    n_ascii = 65 or
                    n_ascii = 69 or
                    n_ascii = 73 or
                    n_ascii = 79 or
                    n_ascii = 85 ) then
                        v_vocali := concat(v_vocali,v_cara);
                    else
                        v_consonanti := concat(v_consonanti,v_cara);
                        y := y + 1;
                end if;  
            end if;
            i := i + 1;
        end loop;
        --assegno il valore del codice fiscale
        v_cod_fis := substr(concat(v_consonanti,v_vocali), 1, 3);

        while length(v_cod_fis) < 3  loop 
            v_cod_fis := concat(v_cod_fis, 'X');
        end loop;
 
        RETURN(v_cod_fis);

        EXCEPTION WHEN OTHERS THEN RETURN('CF-001');
END;
$BODY$
LANGUAGE plpgsql STABLE STRICT
COST 100;

ALTER FUNCTION public.cf_calcolo_cognome(text) OWNER TO sharengo;
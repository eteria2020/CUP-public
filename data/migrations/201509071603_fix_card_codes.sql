CREATE OR REPLACE FUNCTION fix_card_code(crfid text, ccode text)
    RETURNS void
    LANGUAGE plpgsql
    AS
    $$
        DECLARE customer_id int;
        DECLARE origin_code text;
        BEGIN
            origin_code = ccode;
            ccode = substring(ccode from 1 for 8);
            IF (ccode != origin_code) THEN
                customer_id = (SELECT customers.id FROM customers WHERE customers.card_code LIKE (ccode || '%'));
                UPDATE customers SET card_code = NULL WHERE id = customer_id;
                UPDATE cards SET code = ccode WHERE rfid = crfid;
                UPDATE customers SET card_code = ccode WHERE id = customer_id;
            END IF;
        END;
    $$;

SELECT fix_card_code(cards.code, cards.rfid) FROM cards;

ALTER TABLE cards ADD CONSTRAINT alnum_code CHECK (code ~* '^[A-Z0-9]+$');

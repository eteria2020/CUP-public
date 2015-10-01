/**
 * @param text crfid the rfid of a card
 * @param text ccode the code of a card
 *
 * Checks if the card's code is of the right length by extracting the longest
 * substring. If it isn't, it will:
 * - retrieve the customer with that code,
 * - remove the card from the customer,
 * - update the card with the correct code,
 * - reassign the card to the customer
 */
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

/**
 * Calls the fix_card_code() function for every card, passing the rfid and code
 */
SELECT fix_card_code(cards.rfid, cards.code) FROM cards;

/**
 * Adds a constraint to the cards code column so that only capital alphanumeric
 * values can be used
 */
ALTER TABLE cards ADD CONSTRAINT alnum_code CHECK (code ~ '^[A-Z0-9]+$');

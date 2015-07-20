ALTER TABLE customers ADD general_condition1 BOOLEAN DEFAULT null;
ALTER TABLE customers ADD general_condition2 BOOLEAN DEFAULT null;
ALTER TABLE customers ADD regulation_condition1 BOOLEAN DEFAULT null;
ALTER TABLE customers ADD regulation_condition2 BOOLEAN DEFAULT null;
ALTER TABLE customers ADD privacy_condition BOOLEAN DEFAULT null;
ALTER TABLE customers ADD commercial_condition1 BOOLEAN DEFAULT false;
ALTER TABLE customers ADD commercial_condition2 BOOLEAN DEFAULT false;

UPDATE customers
    SET general_condition1 = true,
        general_condition2 = true,
        regulation_condition1 = true,
        regulation_condition2 = true,
        privacy_condition = true,
        commercial_condition1 = false,
        commercial_condition2 = false;

ALTER TABLE customers ALTER COLUMN general_condition1 SET NOT NULL;
ALTER TABLE customers ALTER COLUMN general_condition2 SET NOT NULL;
ALTER TABLE customers ALTER COLUMN regulation_condition1 SET NOT NULL;
ALTER TABLE customers ALTER COLUMN regulation_condition2 SET NOT NULL;
ALTER TABLE customers ALTER COLUMN privacy_condition SET NOT NULL;
ALTER TABLE customers ALTER COLUMN commercial_condition1 SET NOT NULL;
ALTER TABLE customers ALTER COLUMN commercial_condition2 SET NOT NULL;

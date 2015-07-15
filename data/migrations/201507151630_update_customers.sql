ALTER TABLE customers ADD general_condition_read BOOLEAN DEFAULT false;
ALTER TABLE customers ADD general_condition_accepted BOOLEAN DEFAULT false;
ALTER TABLE customers ADD regulation_condition_read BOOLEAN DEFAULT false;
ALTER TABLE customers ADD regulation_condition_accepted BOOLEAN DEFAULT false;
ALTER TABLE customers ADD privacy_condition_accepted BOOLEAN DEFAULT false;
ALTER TABLE customers ADD commercial_condition1_accepted BOOLEAN DEFAULT false;
ALTER TABLE customers ADD commercial_condition2_accepted BOOLEAN DEFAULT false;

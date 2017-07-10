--Add COLUMN in table customer to field checkbox privacy_information
ALTER TABLE customers  ADD COLUMN  privacy_information boolean DEFAULT false NOT NULL;

--Add COLUMN in table customer to field checkbox newsletter
ALTER TABLE customers  ADD COLUMN  newsletter boolean DEFAULT false NOT NULL;
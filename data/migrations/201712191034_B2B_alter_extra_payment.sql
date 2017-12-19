ALTER TABLE business.extra_payment ALTER currency TYPE TEXT;
ALTER TABLE business.extra_payment ALTER status   TYPE TEXT;

ALTER TABLE business.extra_payment ADD COLUMN invoice_at  timestamp(0) without time zone DEFAULT NOW();
ALTER TABLE business.extra_payment ADD COLUMN fleet_id  INT;
ALTER TABLE business.extra_payment ALTER fleet_id  SET NOT NULL;
ALTER TABLE business.extra_payment ADD CONSTRAINT fleet_fk FOREIGN KEY (fleet_id) REFERENCES public.fleets(id);
ALTER TABLE business.extra_payment ADD COLUMN invoce_able  BOOLEAN DEFAULT TRUE;
ALTER TABLE business.extra_payment ALTER invoce_able  SET NOT NULL;

CREATE TYPE business.business_extra_payment_types AS ENUM('extra','penality','credit_card_change');
ALTER TABLE business.extra_payment ADD COLUMN payment_type business.business_extra_payment_types;
ALTER TABLE business.extra_payment ALTER payment_type  SET NOT NULL;

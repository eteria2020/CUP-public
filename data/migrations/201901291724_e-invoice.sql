ALTER TABLE "public"."customers" ADD COLUMN "recipient_code" text;
COMMENT ON COLUMN "public"."customers"."recipient_code" IS 'Fattura elettronica: codice destinatario';

ALTER TABLE "public"."customers" ADD COLUMN "cem" text;
COMMENT ON COLUMN "public"."customers"."cem" IS 'Fattura elettronica: Certified EMail (Posta Elettronica Certificata PEC)';

ALTER TABLE "business"."business" ADD COLUMN "recipient_code" text;
COMMENT ON COLUMN "business"."business"."recipient_code" IS 'Fattura elettronica: codice destinatario';

ALTER TABLE "business"."business" ADD COLUMN "cem" text;
COMMENT ON COLUMN "business"."business"."cem" IS 'Fattura elettronica: Certified EMail (Posta Elettronica Certificata PEC)';
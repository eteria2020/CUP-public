
ALTER TABLE "public"."penalties" ADD COLUMN "logistic_description" text;
ALTER TABLE "public"."penalties" ADD COLUMN "logistic" boolean DEFAULT true NOT NULL;
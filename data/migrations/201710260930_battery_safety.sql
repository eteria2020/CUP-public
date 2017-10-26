ALTER TABLE cars ADD COLUMN battery_safety_ts timestamp(0) with time zone default NULL;

CREATE OR REPLACE FUNCTION public.f_battery_safety_ts()
RETURNS TRIGGER AS $$
BEGIN
IF (NEW.battery_safety <> OLD.battery_safety) THEN
   NEW.battery_safety_ts = now();
END IF;
   RETURN NEW;
END;
$$ language 'plpgsql';

ALTER FUNCTION public.f_battery_safety_ts() OWNER TO sharengo;

CREATE TRIGGER trigger_battery_safety_ts BEFORE UPDATE ON cars FOR EACH ROW EXECUTE PROCEDURE f_battery_safety_ts();
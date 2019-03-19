
/* OLD cost_steps {"60": 1200, "1440": 5000}  */
UPDATE fares SET cost_steps = ('{"1440": 5000}')::jsonb WHERE id=1;
ALTER TABLE configurations ADD COLUMN config_spec JSONB;
UPDATE configurations SET config_spec='[{"fleet_id": 4, "battery": 35}, {"fleet_id": 3, "battery": 30}]' WHERE config_key='battery';
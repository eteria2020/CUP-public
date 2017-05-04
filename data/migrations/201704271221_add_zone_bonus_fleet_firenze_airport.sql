INSERT INTO zone_bonus_fleets (zone_bonus_id, fleet_id)
VALUES ((select max(id)+1 from zone_bonus), 2)
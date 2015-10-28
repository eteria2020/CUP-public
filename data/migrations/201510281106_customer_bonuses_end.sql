update customers_bonus
set valid_to = (valid_to + '23 hours 59 minutes 59 seconds')
where to_char(valid_to, 'HH24:MI:SS') = '00:00:00';
#!/bin/sh
#clean old commands and old archive reservations

time='3 months'

PSQL='psql -p 5433'

$PSQL <<THE_END
DELETE FROM commands
WHERE queued < (now() - interval'$time');
DELETE FROM reservations_archive
WHERE ts < (now() - interval'$time');
DELETE FROM customer_locations
WHERE timestamp < (now() - interval '10 month');
THE_END
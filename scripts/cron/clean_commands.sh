#!/bin/sh
#clean old commands and old archive reservations

time='1 year'

PSQL='psql -p 5433'

$PSQL <<THE_END
DELETE FROM commands
WHERE queued < (now() - interval'$time');
DELETE FROM reservations_archive
WHERE ts < (now() - interval'$time');
THE_END

#!/bin/sh
PSQL='psql -p 5433'


$PSQL <<THE_END
DELETE FROM commands
WHERE queued <(now() - interval'1 year') AND to_send=false;
THE_END
#!/bin/sh
#call the function that add minutes to silver members

PSQL='psql -p 5433'

$PSQL <<THE_END
SELECT "addBonusSilverList"();
THE_END
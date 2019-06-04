#!/bin/sh
#call the function that add minutes to silver members
PSQL='psql -p 5433 -c '
$PSQL  "SELECT \"addBonusSilverList\"();"


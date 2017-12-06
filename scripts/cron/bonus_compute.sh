#/usr/local/scripts/bonus_compute.sh (CALCOLA IL BONUS DI OGNI SIGNOLA CORSA)

#!/bin/sh
#

ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/log/bonus_compute.log
php /srv/apps/sharengo-publicsite/public/index.php bonus compute >> /srv/apps/sharengo-publicsite/data/log/bonus_compute.log
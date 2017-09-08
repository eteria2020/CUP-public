#!/bin/sh
#

ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/account_compute.log
php /srv/apps/sharengo-publicsite/public/index.php account compute >> /srv/apps/sharengo-publicsite/data/account_compute.log
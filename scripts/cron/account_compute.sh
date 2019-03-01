#!/bin/sh
#

ts=$(date +'%D %T')
echo "$ts ------------------" >> /var/log/sharengo-publicsite/data/log/account_compute.log
php /srv/apps/sharengo-publicsite/public/index.php account compute >> /var/log/sharengo-publicsite/data/log/account_compute.log

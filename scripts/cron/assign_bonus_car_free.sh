#!/bin/sh
ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/log/assign-bonus-car-free.log
php /srv/apps/sharengo-publicsite/public/index.php assign bonus car free  >> /srv/apps/sharengo-publicsite/data/log/assign-bonus-car-free.log
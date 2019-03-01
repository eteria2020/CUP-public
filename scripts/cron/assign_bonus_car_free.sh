#!/bin/sh
ts=$(date +'%D %T')
echo "$ts ------------------" >> /var/log/sharengo-publicsite/data/log/assign-bonus-car-free.log
php /srv/apps/sharengo-publicsite/public/index.php assign bonus car free  >> /var/log/sharengo-publicsite/data/log/assign-bonus-car-free.log

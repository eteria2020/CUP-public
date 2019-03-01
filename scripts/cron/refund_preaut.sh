#!/bin/sh
#

ts=$(date +'%D %T')
echo "$ts ------------------" >> /var/log/sharengo-publicsite/data/log/refund_preaut.log
php /srv/apps/sharengo-publicsite/public/index.php refund preaut | tee -a /var/log/sharengo-publicsite/data/log/refund_preaut.log

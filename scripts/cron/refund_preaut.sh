#!/bin/sh
#

ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/log/refund_preaut.log
php /srv/apps/sharengo-publicsite/public/index.php refund preaut | tee -a /srv/apps/sharengo-publicsite/data/log/refund_preaut.log
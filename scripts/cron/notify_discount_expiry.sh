#!/bin/sh
#

ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/notify_disable_discount.log
php /srv/apps/sharengo-publicsite/public/index.php notify disable discount  | tee -a /srv/apps/sharengo-publicsite/data/notify_disable_discount.log
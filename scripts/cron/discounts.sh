#!/bin/sh
#

#ts=$(date +'%D %T')
#echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/log/discounts_notify.log
#php /srv/apps/sharengo-publicsite/public/index.php notify disable discount  | tee -a /srv/apps/sharengo-publicsite/data/log/discounts.log

#ts=$(date +'%D %T')
#echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/log/discounts_disable.log
#php /srv/apps/sharengo-publicsite/public/index.php disable old discounts | tee -a /srv/apps/sharengo-publicsite/data/log/discounts.log

php /srv/apps/sharengo-publicsite/public/index.php renew old discounts | tee -a /srv/apps/sharengo-publicsite/data/log/discounts.log
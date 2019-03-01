#!/bin/sh
#

#ts=$(date +'%D %T')
#echo "$ts ------------------" >> /var/log/sharengo-publicsite/data/log/discounts_notify.log
#php /srv/apps/sharengo-publicsite/public/index.php notify disable discount  | tee -a /var/log/sharengo-publicsite/data/log/discounts.log

#ts=$(date +'%D %T')
#echo "$ts ------------------" >> /var/log/sharengo-publicsite/data/log/discounts_disable.log
#php /srv/apps/sharengo-publicsite/public/index.php disable old discounts | tee -a /var/log/sharengo-publicsite/data/log/discounts.log

php /srv/apps/sharengo-publicsite/public/index.php renew old discounts | tee -a /var/log/sharengo-publicsite/data/log/discounts.log

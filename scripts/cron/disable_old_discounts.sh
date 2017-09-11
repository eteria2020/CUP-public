#!/bin/sh
#

ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/disable_old_discounts.log
php /srv/apps/sharengo-publicsite/public/index.php disable old discounts | tee -a /srv/apps/sharengo-publicsite/data/log/disable_old_discounts.log
#!/bin/sh
#

ts=$(date +'%D %T')
echo "$ts ------------------" >> /var/log/sharengo-publicsite/data/log/partner_notify_nugo.log
php /srv/apps/sharengo-publicsite/public/index.php partner notify customer --partner='nugo' >> /var/log/sharengo-publicsite/data/log/partner_notify_nugo.log

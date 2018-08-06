#!/bin/sh
#

ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/log/partner_notify_nugo.log
php /srv/apps/sharengo-publicsite/public/index.php partner notify customer --partner='nugo' >> /srv/apps/sharengo-publicsite/data/log/partner_notify_nugo.log
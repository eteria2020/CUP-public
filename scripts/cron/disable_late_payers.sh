#!/bin/sh
#

ts=$(date +'%D %T')
echo "$ts ------------------" >> /var/log/sharengo-publicsite/data/log/disable_late_payers.log
php /srv/apps/sharengo-publicsite/public/index.php disable late payers -v >> /var/log/sharengo-publicsite/data/log/disable_late_payers.log

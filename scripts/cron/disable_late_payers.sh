#!/bin/sh
#

ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/disable_late_payers.log
php /srv/apps/sharengo-publicsite/public/index.php disable late payers -v >> /srv/apps/sharengo-publicsite/data/log/disable_late_payers.log
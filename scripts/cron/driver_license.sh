#!/bin/sh
#
ts=$(date +%Y-%m-%d) #time_stamp

php /srv/apps/sharengo-publicsite/public/index.php disable customers expired license >> /srv/apps/sharengo-publicsite/data/log/driver_license.log
php /srv/apps/sharengo-publicsite/public/index.php periodic check valid license      >> /srv/apps/sharengo-publicsite/data/log/driver_license.log
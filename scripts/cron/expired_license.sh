#!/bin/sh
#
ts=$(date +%Y-%m-%d) #time_stamp

php /srv/apps/sharengo-publicsite/public/index.php disable customers expired license >> /srv/apps/sharengo-publicsite/data/log/expired_license.log
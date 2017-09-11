#!/bin/sh
#

ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/export_registries.log
php /srv/apps/sharengo-publicsite/public/index.php export registries >> /srv/apps/sharengo-publicsite/data/log/export_registries.log
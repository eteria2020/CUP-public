#!/bin/sh
#

ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/log/export_registries.log
php /srv/apps/sharengo-publicsite/public/index.php export registries >> /srv/apps/sharengo-publicsite/data/log/export_registries.log

php /srv/apps/sharengo-publicsite/public/index.php business-export registries >> /srv/apps/sharengo-publicsite/data/log/business_export_registries.log
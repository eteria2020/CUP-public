#!/bin/sh
#

ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/business_check_groups_limits.log
php /srv/apps/sharengo-publicsite/public/index.php business check groups limits >> /srv/apps/sharengo-publicsite/data/business_check_groups_limits.log

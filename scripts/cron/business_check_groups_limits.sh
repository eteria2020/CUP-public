#!/bin/sh
#

ts=$(date +'%D %T')
echo "$ts ------------------" >> /var/log/sharengo-publicsite/data/log/business_check_groups_limits.log
php /srv/apps/sharengo-publicsite/public/index.php business check groups limits >> /var/log/sharengo-publicsite/data/log/business_check_groups_limits.log

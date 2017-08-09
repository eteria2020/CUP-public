#!/bin/sh
#

ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/assign-birthday-bonuses.log
php /srv/apps/sharengo-publicsite/public/index.php assign birthday bonuses >> /srv/apps/sharengo-publicsite/data/assign-birthday-bonuses.log
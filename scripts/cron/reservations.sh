#!/bin/sh
#sv 20150715
#
php /srv/apps/sharengo-publicsite/public/index.php archive reservations --verbose >> /var/log/sharengo-publicsite/data/maintenance/reservations.log
echo "------------------" >> /var/log/sharengo-publicsite/data/maintenance/reservations.log

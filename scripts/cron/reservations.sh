#!/bin/sh
#sv 20150715
#
php /srv/apps/sharengo-publicsite/public/index.php archive reservations --verbose >> /srv/apps/sharengo-publicsite/data/maintenance/reservations.log
echo "------------------" >> /srv/apps/sharengo-publicsite/data/maintenance/reservations.log
#!/bin/sh
#

ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/log/invoice_trip.log
php /srv/apps/sharengo-publicsite/public/index.php generate trip invoices monthly >> /srv/apps/sharengo-publicsite/data/log/invoice_trip.log
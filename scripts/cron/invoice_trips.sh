#!/bin/sh
#

ts=$(date +'%D %T')
echo "$ts ------------------" >> /var/log/sharengo-publicsite/data/log/invoice_trip.log
php /srv/apps/sharengo-publicsite/public/index.php generate trip invoices monthly >> /var/log/sharengo-publicsite/data/log/invoice_trip.log

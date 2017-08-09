#!/bin/sh
#

#su - postgres -c 'pg_dump -T events -T logs sharengo > sharengo_dump_$(date +"%Y-%m-%d").sql'

ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/pay_invoice.log
php /srv/apps/sharengo-publicsite/public/index.php generate locations
php /srv/apps/sharengo-publicsite/public/index.php pay invoice | tee -a /srv/apps/sharengo-publicsite/data/pay_invoice.log
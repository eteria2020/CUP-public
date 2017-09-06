#!/bin/sh
#

ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/log/business_generate_invoices.log
php /srv/apps/sharengo-publicsite/public/index.php generate business invoices >> /srv/apps/sharengo-publicsite/data/log/business_generate_invoices.log
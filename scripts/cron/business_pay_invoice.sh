#!/bin/sh
#

ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/business_pay_invoice.log
php /srv/apps/sharengo-publicsite/public/index.php business pay invoice >> /srv/apps/sharengo-publicsite/data/business_pay_invoice.log
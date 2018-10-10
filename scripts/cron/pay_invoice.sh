#!/bin/sh
#

#su - postgres -c 'pg_dump -T events -T logs sharengo > sharengo_dump_$(date +"%Y-%m-%d").sql'

ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/log/pay_invoice.log
php /srv/apps/sharengo-publicsite/public/index.php generate locations
php /srv/apps/sharengo-publicsite/public/index.php pay invoice | tee -a /srv/apps/sharengo-publicsite/data/log/pay_invoice.log

#ts=$(date +'%D %T')
#echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/log/pay_invoice_wrong.log
#php /srv/apps/sharengo-publicsite/public/index.php retry wrong payments | tee -a /srv/apps/sharengo-publicsite/data/log/pay_invoice_wrong.log

ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/log/business_pay_invoice.log
php /srv/apps/sharengo-publicsite/public/index.php business pay invoice >> /srv/apps/sharengo-publicsite/data/log/business_pay_invoice.log

# moved into extra.sh
#ts=$(date +'%D %T')
#echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/log/pay_invoice_extra.log
#php /srv/apps/sharengo-publicsite/public/index.php pay invoice extra | tee -a /srv/apps/sharengo-publicsite/data/log/pay_invoice_extra.log
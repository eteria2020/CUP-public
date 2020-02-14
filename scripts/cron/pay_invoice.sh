#!/bin/sh
#

#su - postgres -c 'pg_dump -T events -T logs sharengo > sharengo_dump_$(date +"%Y-%m-%d").sql'

#now=$(date +%s)
#no_run_init=$(date -d "2019-03-31 00:00:00" +%s)
#no_run_end=$(date -d "2019-03-31 07:30:00" +%s)
#
## block the run if now>="2019-03-31 00:00:00" and now<="2019-03-31 07:30:00"
#if [ $now -ge $no_run_init ] && [ $now -le $no_run_end ]; then
#    ts=$(date +'%D %T')
#    echo "$ts ----- NO RUN ---------" >> /var/log/sharengo-publicsite/data/log/pay_invoice.log
#    exit 1
#fi

ts=$(date +'%D %T')
echo "$ts ------------------" >> /var/log/sharengo-publicsite/data/log/generate_locations.log
php /srv/apps/sharengo-publicsite/public/index.php generate locations | tee -a /var/log/sharengo-publicsite/data/log/generate_locations.log

ts=$(date +'%D %T')
echo "$ts ------------------" >> /var/log/sharengo-publicsite/data/log/pay_invoice.log
php /srv/apps/sharengo-publicsite/public/index.php pay invoice | tee -a /var/log/sharengo-publicsite/data/log/pay_invoice.log

#ts=$(date +'%D %T')
#echo "$ts ------------------" >> /var/log/sharengo-publicsite/data/log/pay_invoice_wrong.log
#php /srv/apps/sharengo-publicsite/public/index.php retry wrong payments | tee -a /var/log/sharengo-publicsite/data/log/pay_invoice_wrong.log

ts=$(date +'%D %T')
echo "$ts ------------------" >> /var/log/sharengo-publicsite/data/log/business_pay_invoice.log
php /srv/apps/sharengo-publicsite/public/index.php business pay invoice >> /var/log/sharengo-publicsite/data/log/business_pay_invoice.log

# moved into extra.sh
#ts=$(date +'%D %T')
#echo "$ts ------------------" >> /var/log/sharengo-publicsite/data/log/pay_invoice_extra.log
#php /srv/apps/sharengo-publicsite/public/index.php pay invoice extra | tee -a /var/log/sharengo-publicsite/data/log/pay_invoice_extra.log

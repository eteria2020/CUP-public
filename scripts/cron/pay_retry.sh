#!/bin/sh
#

now=$(date +%s)
no_run_init=$(date -d "2019-03-31 00:00:00" +%s)
no_run_end=$(date -d "2019-03-31 07:30:00" +%s)

# block the run if now>="2019-03-31 00:00:00" and now<="2019-03-31 07:30:00"
if [ $now -ge $no_run_init ] && [ $now -le $no_run_end ]; then
    ts=$(date +'%D %T')
    echo "$ts ----- NO RUN ---------" >> /var/log/sharengo-publicsite/data/log/pay_invoice_wrong.log
    exit 1
fi

ts=$(date +'%D %T')
echo "$ts ------------------" >> /var/log/sharengo-publicsite/data/log/pay_invoice_wrong.log
php /srv/apps/sharengo-publicsite/public/index.php retry wrong payments | tee -a /var/log/sharengo-publicsite/data/log/pay_invoice_wrong.log

#moved into extra.sh
#ts=$(date +'%D %T')
#echo "$ts ------------------" >> /var/log/sharengo-publicsite/data/log/pay_invoice_wrong_extra.log
#php /srv/apps/sharengo-publicsite/public/index.php retry wrong extra | tee -a /var/log/sharengo-publicsite/data/log/pay_invoice_wrong_extra.log

#!/bin/sh
#

now=$(date +%s)
no_run_init=$(date -d "2019-05-26 00:00:00" +%s)
no_run_end=$(date -d "2019-05-26 07:30:00" +%s)

# block the run if now>="2019-05-26 00:00:00" and now<="2019-05-26 07:30:00"
if [ $now -ge $no_run_init ] && [ $now -le $no_run_end ]; then
    ts=$(date +'%D %T')
    echo "$ts ----- NO RUN ---------" >> /var/log/sharengo-publicsite/data/log/pay_invoice_wrong.log
    exit 1
fi

php /srv/apps/sharengo-publicsite/public/index.php retry wrong payments time '' '' | tee -a /var/log/sharengo-publicsite/data/log/pay_invoice_wrong.log

#!/bin/sh
#

ts=$(date +'%D %T')
echo "$ts ------------------" >> /var/log/sharengo-publicsite/data/log/pay_invoice_wrong.log
php /srv/apps/sharengo-publicsite/public/index.php retry wrong payments | tee -a /var/log/sharengo-publicsite/data/log/pay_invoice_wrong.log

#moved into extra.sh
#ts=$(date +'%D %T')
#echo "$ts ------------------" >> /var/log/sharengo-publicsite/data/log/pay_invoice_wrong_extra.log
#php /srv/apps/sharengo-publicsite/public/index.php retry wrong extra | tee -a /var/log/sharengo-publicsite/data/log/pay_invoice_wrong_extra.log

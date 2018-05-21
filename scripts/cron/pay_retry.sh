#!/bin/sh
#

ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/log/pay_invoice_wrong.log
php /srv/apps/sharengo-publicsite/public/index.php retry wrong payments | tee -a /srv/apps/sharengo-publicsite/data/log/pay_invoice_wrong.log

ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/log/pay_invoice_wrong_extra.log
php /srv/apps/sharengo-publicsite/public/index.php retry wrong extra | tee -a /srv/apps/sharengo-publicsite/data/log/pay_invoice_wrong_extra.log

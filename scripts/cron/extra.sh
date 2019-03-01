#!/bin/sh
#

php /srv/apps/sharengo-publicsite/public/index.php retry wrong extra time '' '' | tee -a /var/log/sharengo-publicsite/data/log/pay_invoice_wrong_extra.log

ts=$(date +'%D %T')
echo "$ts ------------------" >> /var/log/sharengo-publicsite/data/log/pay_invoice_extra.log
php /srv/apps/sharengo-publicsite/public/index.php pay invoice extra | tee -a /var/log/sharengo-publicsite/data/log/pay_invoice_extra.log

echo "$ts ------------------" >> /var/log/sharengo-publicsite/data/log/pay_extra_rates.log
php /srv/apps/sharengo-publicsite/public/index.php payment rates | tee -a /var/log/sharengo-publicsite/data/log/pay_extra_rates.log

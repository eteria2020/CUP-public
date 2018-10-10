#!/bin/sh
#

php /srv/apps/sharengo-publicsite/public/index.php retry wrong extra time '' '' | tee -a /srv/apps/sharengo-publicsite/data/log/pay_invoice_wrong_extra.log

ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/log/pay_invoice_wrong_extra.log
php /srv/apps/sharengo-publicsite/public/index.php retry wrong extra | tee -a /srv/apps/sharengo-publicsite/data/log/pay_invoice_wrong_extra.log

ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/log/pay_invoice_extra.log
php /srv/apps/sharengo-publicsite/public/index.php pay invoice extra | tee -a /srv/apps/sharengo-publicsite/data/log/pay_invoice_extra.log

ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/log/invoice_extra.log
php /srv/apps/sharengo-publicsite/public/index.php generate extra invoices >> /srv/apps/sharengo-publicsite/data/log/invoice_extra.log
php /srv/apps/sharengo-publicsite/public/index.php generate package invoices >> /srv/apps/sharengo-publicsite/data/log/invoice_package.log
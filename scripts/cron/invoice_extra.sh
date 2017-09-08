#!/bin/sh
#

ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/invoice_extra.log
php /srv/apps/sharengo-publicsite/public/index.php generate extra invoices >> /srv/apps/sharengo-publicsite/data/invoice_extra.log
php /srv/apps/sharengo-publicsite/public/index.php generate package invoices >> /srv/apps/sharengo-publicsite/data/invoice_package.log
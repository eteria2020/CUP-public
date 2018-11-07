#!/bin/sh
#

php /srv/apps/sharengo-publicsite/public/index.php invoice registrations --verbose >> /srv/apps/sharengo-publicsite/data/maintenance/registration_invoices.log
echo "------------------" >> /srv/apps/sharengo-publicsite/data/maintenance/registration_invoices.log

ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/log/invoice_extra.log
php /srv/apps/sharengo-publicsite/public/index.php generate extra   invoices >> /srv/apps/sharengo-publicsite/data/log/invoice_extra.log
php /srv/apps/sharengo-publicsite/public/index.php generate package invoices >> /srv/apps/sharengo-publicsite/data/log/invoice_package.log

#!/bin/sh
#

php /srv/apps/sharengo-publicsite/public/index.php invoice registrations --verbose >> /var/log/sharengo-publicsite/data/maintenance/registration_invoices.log
echo "------------------" >> /var/log/sharengo-publicsite/data/maintenance/registration_invoices.log

ts=$(date +'%D %T')
echo "$ts ------------------" >> /var/log/sharengo-publicsite/data/log/invoice_extra.log
php /srv/apps/sharengo-publicsite/public/index.php generate extra   invoices >> /var/log/sharengo-publicsite/data/log/invoice_extra.log
php /srv/apps/sharengo-publicsite/public/index.php generate package invoices >> /var/log/sharengo-publicsite/data/log/invoice_package.log

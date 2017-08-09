#!/bin/sh
#sv 20150731
#
php /srv/apps/sharengo-publicsite/public/index.php invoice registrations --verbose >> /srv/apps/sharengo-publicsite/data/maintenance/registration_invoices.log
echo "------------------" >> /srv/apps/sharengo-publicsite/data/maintenance/registration_invoices.log
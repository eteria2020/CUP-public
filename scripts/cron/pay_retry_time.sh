#!/bin/sh
#

php /srv/apps/sharengo-publicsite/public/index.php retry wrong payments time '' '' | tee -a /var/log/sharengo-publicsite/data/log/pay_invoice_wrong.log

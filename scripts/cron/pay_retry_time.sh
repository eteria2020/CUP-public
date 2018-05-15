#!/bin/sh
#

php /srv/apps/sharengo-publicsite/public/index.php retry wrong payments time '' '' | tee -a /srv/apps/sharengo-publicsite/data/log/pay_invoice_wrong.log

#php /srv/apps/sharengo-publicsite/public/index.php retry wrong extra time '' '' | tee -a /srv/apps/sharengo-publicsite/data/log/pay_invoice_wrong_extra.log

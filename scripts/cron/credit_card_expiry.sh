#!/bin/sh
#

ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/log/credit_card_expiry.log

php /srv/apps/sharengo-publicsite/public/index.php business credit card notify >> /srv/apps/sharengo-publicsite/data/log/credit_card_expiry.log

php /srv/apps/sharengo-publicsite/public/index.php business credit card expiry >> /srv/apps/sharengo-publicsite/data/log/credit_card_expiry.log

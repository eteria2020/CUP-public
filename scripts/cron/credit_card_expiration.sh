#!/bin/sh

#this script alert user that your credit card in in the following month will be disabled
php /srv/apps/sharengo-publicsite/public/index.php aletr credit card expiration  >> /srv/apps/sharengo-publicsite/data/log/alert_credit_card_expiration.log

#this script disable the contract and user if your crfedit card is expiration
php /srv/apps/sharengo-publicsite/public/index.php disable credit card  >> /srv/apps/sharengo-publicsite/data/log/disable_credit_card.log
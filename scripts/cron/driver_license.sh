#!/bin/sh

php /srv/apps/sharengo-publicsite/public/index.php disable customers expired license >> /var/log/sharengo-publicsite/data/log/driver_license.log
php /srv/apps/sharengo-publicsite/public/index.php periodic check valid license      >> /var/log/sharengo-publicsite/data/log/driver_license.log

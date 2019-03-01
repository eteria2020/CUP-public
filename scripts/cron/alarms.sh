#!/bin/sh
#xian 20150701
#
php /srv/apps/sharengo-publicsite/public/index.php check alarms --verbose >> /var/log/sharengo-publicsite/data/maintenance/alarms.log
echo "------------------" >> /var/log/sharengo-publicsite/data/maintenance/alarms.log

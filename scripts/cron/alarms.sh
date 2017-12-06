#!/bin/sh
#xian 20150701
#
php /srv/apps/sharengo-publicsite/public/index.php check alarms --verbose >> /srv/apps/sharengo-publicsite/data/maintenance/alarms.log
echo "------------------" >> /srv/apps/sharengo-publicsite/data/maintenance/alarms.log
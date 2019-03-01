#Close maintainer's open trips

#!/bin/sh
#

php /srv/apps/sharengo-publicsite/public/index.php close old trip maintainer >> /var/log/sharengo-publicsite/data/log/close_old_trip_maintainers.log

#!/bin/sh
#sv 20170206
#
ts=$(date +%Y-%m-%d) #time_stamp
#ts2=2017-02-24
radius=100 #0.001242 #raggio dal pois
carplate=all
php /srv/apps/sharengo-publicsite/public/index.php bonus park $ts $radius $carplate >> /srv/apps/sharengo-publicsite/data/log/bonus_park.log
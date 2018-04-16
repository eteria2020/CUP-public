#!/bin/sh

#####################################################################################################################################################
#BONUS NIVEA
php /srv/apps/sharengo-publicsite/public/index.php bonus nivea  >> /srv/apps/sharengo-publicsite/data/log/bonus_nivea.log
#####################################################################################################################################################



#####################################################################################################################################################
#BONUS PARK
#sv 20170206
#
ts=$(date +%Y-%m-%d) #time_stamp
#ts2=2017-02-24
radius=100 #0.001242 #raggio dal pois
carplate=all
php /srv/apps/sharengo-publicsite/public/index.php bonus park $ts $radius $carplate >> /srv/apps/sharengo-publicsite/data/log/bonus_park.log
#####################################################################################################################################################



#####################################################################################################################################################
#ASSIGN BIRTHDAY BONUSES
ts=$(date +'%D %T')
echo "$ts ------------------" >> /srv/apps/sharengo-publicsite/data/log/assign-birthday-bonuses.log
php /srv/apps/sharengo-publicsite/public/index.php assign birthday bonuses >> /srv/apps/sharengo-publicsite/data/log/assign-birthday-bonuses.log
#####################################################################################################################################################



#####################################################################################################################################################
#BONUS ALGEBRIS
#php /srv/apps/sharengo-publicsite/public/index.php bonus algebris  >> /srv/apps/sharengo-publicsite/data/log/bonus_algebris.log
#####################################################################################################################################################
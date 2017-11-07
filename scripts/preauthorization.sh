#!/bin/sh
#
date=`date '+%Y-%m-%d %H:%M:%S'`
customer_id=$1
trip_id=$2
DIR='/srv/apps/sharengo-publicsite'
(timeout 25 php $DIR/public/index.php preauthorization $customer_id $trip_id | grep  -i '}' || echo '{"response":21, "customer_id":$customer_id, "trip_id":$trip_id, "date":"$date"}') | tee -a $DIR/data/log/preauthorization.log
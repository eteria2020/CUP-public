#!/bin/bash

sudo rm /var/log/redis.log

sudo find /var/log/apache2/     -type f -name "*.gz"   -mtime +130 -delete
sudo find /var/log/             -type f -name "*.gz"   -mtime +130 -delete
sudo find /var/www/api/crashes/ -type f -name "*.json" -mtime +10  -exec rm {} \;
sudo find /srv/apps/sharengo-publicsite/public/cache -type f -name "*.png" -mtime +1  -delete
sudo find /srv/apps/sharengo-admin/public/cache      -type f -name "*.*"   -mtime +30 -delete

echo "Mongo db: new log"
pid_of_mongo=$(pidof mongod)
sudo kill -SIGUSR1 $pid_of_mongo
sleep 5

echo "Mongo db: remove old log"
timestamp=$(date +"%Y-%m-%d")
mongo_log_path="/var/log/mongodb/"

echo "rm -rf "$mongo_log_path"mongodb.log."$timestamp"T*"
sudo rm -rf "$mongo_log_path"mongodb.log."$timestamp"T*

echo "Clear queue of Driver License Validation log"
sudo rm    /var/log/sharengo-publicsite/data/log/queue-system-dlv.log
sudo touch /var/log/sharengo-publicsite/data/log/queue-system-dlv.log

#!/bin/sh
sudo mongo < /srv/apps/sharengo-publicsite/scripts/cron/clean_mongo.js >> /var/log/sharengo-publicsite/data/log/clean_mongo.log

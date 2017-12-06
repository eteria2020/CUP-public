#!/bin/sh
export PGPASSWORD="gmjk51pa"

#pg_dump -p 5432 -U cs  sharengo | gzip > /var/backups/postgres/pgdump.sql.gz

#mongodump -d sharengo --collection=logs -o - | gzip > /var/backups/mongodb/logs.gz
#mongodump -d sharengo --collection=events -o - | gzip > /var/backups/mongodb/events.gz

#tar -czvf /var/backups/srv.tgz  /srv

tar -czvf /var/backups/configs/etc.tgz /etc

dpkg --get-selections > /var/backups/configs/dpkg-selections.txt
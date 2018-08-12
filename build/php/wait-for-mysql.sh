#!/bin/bash
set -e

until mysql -u$MYSQL_USER -p$MYSQL_PASSWORD -hmysql -e "show databases;" &> /dev/null
do
    echo "Waiting for MySQL..."
    sleep 1
done

echo "MySQL is ready!"

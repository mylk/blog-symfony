cd /application

./build/php/wait-for-mysql.sh

composer self-update
composer install --no-interaction

bin/console doctrine:database:create --if-not-exists
bin/console doctrine:schema:drop --force
bin/console doctrine:schema:create
bin/console doctrine:fixtures:load -n
bin/console assets:install --symlink

chown -R www-data:www-data app/cache
chown -R www-data:www-data var/cache
chown -R www-data:www-data app/logs
chown -R www-data:www-data var/logs
chown -R www-data:www-data web/img/captcha

php-fpm7.2

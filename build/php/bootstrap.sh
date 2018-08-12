cd /application

./build/php/wait-for-mysql.sh

composer install --no-interaction

app/console doctrine:database:create --if-not-exists
app/console doctrine:schema:drop --force
app/console doctrine:schema:create
app/console doctrine:fixtures:load -n
app/console assets:install --symlink

chown -R www-data:www-data app/cache
chown -R www-data:www-data app/logs
chown -R www-data:www-data web/img/captcha

php-fpm7.0
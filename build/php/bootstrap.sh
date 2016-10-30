cd /application

composer install --no-interaction

app/console doctrine:schema:drop --force
app/console doctrine:schema:create
app/console doctrine:fixtures:load -n

php-fpm7.0
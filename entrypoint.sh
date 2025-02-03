#!/bin/bash

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

if [ ! -d "/var/www/html/vendor" ]; then
    composer install
fi

php artisan migrate
php artisan db:seed --class=EventsTableSeeder

exec apache2-foreground

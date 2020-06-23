#!/bin/bash

npm config set cache /var/www/.npm-cache --global
cd /var/www/frontend && npm install && cd ..

#On error no such file entrypoint.sh, execute in terminal - dos2unix .docker\entrypoint.sh
cp .docker/app/.env.test ./backend/.env.test
cp .docker/app/.env ./backend/.env
composer install
php artisan key:generate
php artisan migrate

php-fpm

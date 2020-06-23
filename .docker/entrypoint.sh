#!/bin/bash

#On error no such file entrypoint.sh, execute in terminal - dos2unix .docker\entrypoint.sh
cp .docker/app/.env.test ./backend/.env.test
cp .docker/app/.env ./backend/.env
composer install
php artisan key:generate
php artisan migrate

php-fpm

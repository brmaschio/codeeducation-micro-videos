#!/bin/bash

#On error no such file entrypoint.sh, execute in terminal - dos2unix .docker\entrypoint.sh
# cp .env.example .env
# cp .env.test.example .env.test
composer install
php artisan key:generate
php artisan migrate

php-fpm

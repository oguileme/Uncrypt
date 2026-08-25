#!/bin/sh
set -e

php artisan migrate --force
php artisan db:seed --force

exec "$@"
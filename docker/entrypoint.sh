#!/bin/sh
set -e

composer install --no-interaction --no-progress
php bin/console doctrine:migrations:migrate --no-interaction

exec php -S 0.0.0.0:8000 -t public/

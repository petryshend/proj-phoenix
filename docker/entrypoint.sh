#!/bin/sh
set -e

composer install --no-interaction --no-progress
php bin/console doctrine:migrations:migrate --no-interaction

exec frankenphp run --config /etc/caddy/Caddyfile --adapter caddyfile

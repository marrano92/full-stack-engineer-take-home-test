#!/bin/bash

set -e

# Ensure that the fpm logs end-up on the stderr
ln -sf /dev/stderr /var/log/fpm-access.log
ln -sf /dev/stderr /var/log/fpm-error.log

# Update the DB once ready
until php artisan db:monitor > /dev/null 2>&1; do
  echo "Waiting for database connection..."
  sleep 1
done
php artisan migrate --force

echo "Migrations applied successfully."

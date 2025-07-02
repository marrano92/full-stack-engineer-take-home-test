#!/bin/bash

set -e

# Set the HOST and PORT environment variables to 0.0.0.0 and 3000 respectively if they are not already set
export HOST=${HOST:-0.0.0.0}
export PORT=${PORT:-3000}

# Update the nginx site config template with the environment variables
envsubst '${HOST},${PORT}' < /etc/nginx/sites-available/default.template > /etc/nginx/sites-available/default

# Ensure that the fpm logs end-up on the stderr
ln -sf /dev/stderr /var/log/fpm-access.log
ln -sf /dev/stderr /var/log/fpm-error.log

# Optimize the application (composer, view, config, route, etc.)
php artisan optimize:clear
php artisan optimize

# Start Nginx service
service nginx start

# Start PHP-FPM
php-fpm

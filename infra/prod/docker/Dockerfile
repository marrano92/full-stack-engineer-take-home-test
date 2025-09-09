FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    gettext-base \
    git \
    curl \
    libpng-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    zlib1g-dev \
    libxpm-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libyaml-dev \
    zip \
    unzip \
    libpq-dev \
    postgresql-client \
    nginx \
    dumb-init

# Configure and install GD extension
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install gd

# Install PHP extensions (the yaml extension is installed via PECL as it's not supported by docker-php-ext-install)
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath

# Deploy the php configuration
COPY infra/prod/docker/php.ini /usr/local/etc/php/php.ini

# Configure nginx
COPY infra/prod/docker/nginx-site-config.conf.template /etc/nginx/sites-available/default.template

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Get node
COPY --from=node:latest /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node:latest /usr/local/bin/node /usr/local/bin/node
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm

# Install typescript and vite
RUN npm install -g typescript vite

# Switch to the /var/www folder to start to prepare the deploy of the application
WORKDIR /var/www

# Copy the packages
COPY package.json ./
RUN npm i --include=dev

# Copy composer files first
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader

# Copy the rest of the application
COPY .. .

# Setup production environment
RUN cp envs/.env.production .env

# Final Laravel setup
RUN composer dump-autoload --optimize
RUN vite build

# Setup the required folders for Laravel
RUN \
    mkdir -p /var/www/storage/framework/sessions && \
    mkdir -p /var/www/storage/framework/views && \
    mkdir -p /var/www/storage/framework/cache && \
    mkdir -p /var/www/storage/logs && \
    chown -R www-data:www-data /var/www/storage && \
    chown -R www-data:www-data /var/www/bootstrap/cache && \
    chmod -R 775 /var/www/storage && \
    chmod -R 775 /var/www/bootstrap/cache && \
    php artisan storage:link

# Fix the prod entrypoint permissions
RUN chmod +x /var/www/infra/prod/docker/docker-entrypoint.sh
RUN chmod +x /var/www/infra/prod/docker/db-migrations.sh

ENTRYPOINT ["/usr/bin/dumb-init", "--"]

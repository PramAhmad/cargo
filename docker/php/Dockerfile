FROM php:8.2-fpm

MAINTAINER pram
ENV DEBIAN_FRONTEND=noninteractive

# Install dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    libmemcached-dev \
    libz-dev \
    libpq-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libwebp-dev \
    libxpm-dev \
    libssl-dev \
    libonig-dev \
    libzip-dev \
    gnupg \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install Node.js 20.x
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    npm install -g npm@latest

# Fix for GD installation - make sure all dependencies are available
RUN apt-get update && apt-get install -y \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libpng-dev \
    libgd-dev \
    --no-install-recommends && \
    rm -rf /var/lib/apt/lists/*

# Configure and install GD properly
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp \
    --with-xpm && \
    docker-php-ext-install -j$(nproc) gd

# Install other PHP extensions
RUN docker-php-ext-install zip pcntl pdo pdo_mysql mysqli

# Verify GD installation - this should now work
RUN php -r 'var_dump(gd_info());'

# Copy custom php.ini
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html

# Copy composer from official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html
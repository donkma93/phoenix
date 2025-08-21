FROM php:8.1-fpm

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.lock composer.json /var/www/html/

# Install dependencies, clear cache, and install PHP extensions
RUN apt-get update && apt-get install -y \
    build-essential \
    mariadb-client \
    supervisor \
    nginx \
    libpq-dev \
    libmemcached-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    procps \
    net-tools \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl gd

# Install Node.js
RUN curl -sL https://deb.nodesource.com/setup_12.x | bash - \
    && apt-get install -y nodejs

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for Laravel application
#RUN groupadd -g 1000 www-data \
#    && useradd -u 1000 -ms /bin/bash -g www-data www-data

# Copy existing application directory contents
COPY . /var/www/html

# Copy custom php.ini configuration
COPY ./docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

# Copy Nginx configuration
COPY ./docker/nginx/conf.d/app.conf /etc/nginx/conf.d/default.conf

# Copy supervisord configuration
COPY ./docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Install PHP dependencies
RUN composer install

RUN composer update

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html

# Expose ports
EXPOSE 80 9000

# Start supervisord to manage PHP-FPM and Nginx
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

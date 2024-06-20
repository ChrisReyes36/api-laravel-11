FROM php:8.3.8-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    supervisor -y \
    zip \
    unzip \
    git \
    curl

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql zip

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory contents
COPY .docker/supervisord/production/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

COPY .docker/apache/default.conf /etc/apache2/sites-available/000-default.conf

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy composer.lock and composer.json
COPY composer.lock composer.json ./

# Install dependencies
RUN composer install --no-scripts --prefer-dist

# Expose port 80
EXPOSE 80

# Volumes
VOLUME [ "/var/www/html" ]

# Start Apache
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf", "--pidfile=/dev/null"]

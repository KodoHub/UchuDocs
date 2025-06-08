# Use PHP 8.1 with Apache as base image
FROM php:8.1-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache modules needed for .htaccess
RUN a2enmod rewrite headers expires mime deflate

# Copy .htaccess file
COPY .htaccess /var/www/html/.htaccess

# Set proper permissions for Apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Copy application files
COPY . /var/www/html/

# Install Composer dependencies
RUN composer install --optimize-autoloader --no-dev

# Create necessary directories with proper permissions
RUN mkdir -p docs templates src \
    && chown -R www-data:www-data docs templates src \
    && chmod -R 755 docs templates src

# Expose port 80
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]

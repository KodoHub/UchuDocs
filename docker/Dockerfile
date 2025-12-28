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
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Copy composer from official composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache modules and configure ServerName
RUN a2enmod rewrite headers expires mime deflate \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copy all application files
COPY . .

# Copy .htaccess from docker directory or create a basic one
RUN if [ -f docker/.htaccess ]; then \
        cp docker/.htaccess /var/www/html/.htaccess; \
    else \
        echo "RewriteEngine On" > /var/www/html/.htaccess; \
    fi

# Install PHP dependencies if composer.json exists
RUN if [ -f composer.json ]; then \
        echo "Installing Composer dependencies..." && \
        composer install \
            --optimize-autoloader \
            --no-interaction \
            --ignore-platform-reqs \
            2>&1 | tee /tmp/composer-install.log || \
        (echo "Composer install failed, trying with --no-scripts..." && \
         composer install \
            --optimize-autoloader \
            --no-interaction \
            --no-scripts \
            --ignore-platform-reqs) || \
        echo "Warning: Composer install failed, continuing anyway..."; \
    else \
        echo "No composer.json found, skipping dependency installation"; \
    fi

# Clear composer cache to reduce image size
RUN composer clear-cache 2>/dev/null || true

# Set proper permissions for Apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/storage 2>/dev/null || true \
    && chmod -R 777 /var/www/html/bootstrap/cache 2>/dev/null || true

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]

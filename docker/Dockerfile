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

# Enable Apache modules
RUN a2enmod rewrite headers expires mime deflate

# Copy composer files for dependency caching
COPY composer.json composer.lock* ./

# Create required directories that composer might need
RUN mkdir -p src templates docs

# Copy source files needed for autoloading
COPY src/ ./src/
COPY templates/ ./templates/
COPY docs/ ./docs/

# Install PHP dependencies with optimizations
RUN composer install \
    --optimize-autoloader \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --no-scripts \
    && composer clear-cache

# Now copy the rest of the application
COPY index.php ./
COPY phpcs.xml* ./
COPY version.json* ./
COPY LICENSE* ./
COPY README.md* ./
COPY SECURITY.md* ./

# Copy .htaccess if it exists
COPY docker/.htaccess /var/www/html/.htaccess 2>/dev/null || \
    echo "RewriteEngine On" > /var/www/html/.htaccess

# Run post-install scripts if needed
RUN composer run-script --no-interaction post-autoload-dump 2>/dev/null || true

# Set proper permissions for Apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/storage 2>/dev/null || true \
    && chmod -R 777 /var/www/html/bootstrap/cache 2>/dev/null || true

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]

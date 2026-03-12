# =============================================================
# Dockerfile for TrackNet Inventory Management (Laravel 12)
# =============================================================

# -- BASE IMAGE --
# Start from the official PHP 8.2 image that already has Apache built in.
# This gives us PHP + Apache in one container, similar to how XAMPP works.
FROM php:8.2-apache

# -- SYSTEM DEPENDENCIES --
# Install OS-level packages that PHP extensions need to compile.
#   - libpng, libjpeg, libfreetype = for image handling (GD extension)
#   - libzip-dev = for ZIP files (used by Composer)
#   - unzip = to extract Composer packages
#   - libicu-dev = for internationalization (intl extension)
#   - libonig-dev = for multibyte strings (mbstring extension)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    libicu-dev \
    libonig-dev \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/* \
    && update-ca-certificates \
    && echo "openssl.cafile = /etc/ssl/certs/ca-certificates.crt" > /usr/local/etc/php/conf.d/openssl.ini \
    && echo "curl.cainfo = /etc/ssl/certs/ca-certificates.crt" >> /usr/local/etc/php/conf.d/openssl.ini

# -- PHP EXTENSIONS --
# Install the PHP extensions Laravel needs.
# These are the same extensions you'd enable in php.ini on XAMPP.
#   - pdo_mysql = connect to MySQL
#   - mbstring = multibyte string support
#   - exif = read image metadata
#   - pcntl = process control (for queues)
#   - bcmath = arbitrary precision math
#   - gd = image processing (used by dompdf for PDF generation)
#   - intl = internationalization
#   - zip = ZIP archive support
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    intl \
    zip

# -- APACHE CONFIGURATION --
# Disable conflicting MPM modules and ensure only mpm_prefork is active.
# Enable mod_rewrite for Laravel's clean URLs.
RUN a2dismod mpm_event mpm_worker 2>/dev/null || true \
    && a2enmod mpm_prefork rewrite

# -- DOCUMENT ROOT --
# Tell Apache to serve files from /var/www/html/public (Laravel's public folder).
# In XAMPP, your document root points to htdocs — here we point it to Laravel's public/.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# -- INSTALL COMPOSER --
# Composer is PHP's package manager (like npm for JavaScript).
# We copy it from the official Composer image so we can run `composer install`.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# -- SET WORKING DIRECTORY --
# All subsequent commands will run inside /var/www/html (the project root).
WORKDIR /var/www/html

# -- COPY PROJECT FILES --
# Copy everything from your local project into the container.
COPY . .

# -- INSTALL PHP DEPENDENCIES --
# Run `composer install` to download all packages defined in composer.json.
# --no-dev = skip development packages (we don't need PHPUnit in production)
# --optimize-autoloader = faster class loading
RUN composer install --no-dev --optimize-autoloader

# -- SET PERMISSIONS --
# Laravel needs to write to storage/ and bootstrap/cache/.
# We give Apache (www-data user) ownership of these folders.
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && php artisan storage:link

# -- ENTRYPOINT SCRIPT --
# Copy and enable the entrypoint script that runs migrations and caching at startup.
# This runs AFTER environment variables are injected by Railway.
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# -- EXPOSE PORT --
EXPOSE 80

# -- START --
# Run entrypoint (migrations + caching) then start Apache.
CMD ["/usr/local/bin/docker-entrypoint.sh"]

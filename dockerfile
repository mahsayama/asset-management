# 1. Base Image PHP 7.4 Apache (Matching Production Environment)
FROM php:7.4-apache

# 2. Install extension PHP & dependencies (PostgreSQL, SQLite, MySQLi, Zip, GD, Mbstring)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    libsqlite3-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pdo_sqlite pgsql mysqli zip gd bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Enable Apache mod_rewrite untuk CodeIgniter 3 clean URLs
RUN a2enmod rewrite

# 4. Modifikasi Apache Document Root ke /var/www/html
ENV APACHE_DOCUMENT_ROOT /var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# 5. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Set working directory & copy project files
WORKDIR /var/www/html
COPY . /var/www/html

# 7. Create session directory & set permissions
RUN mkdir -p /tmp/ci_sessions && chown -R www-data:www-data /tmp/ci_sessions

# 8. Install PHP dependencies & set permission
# Use composer update if no lock file exists, otherwise composer install
RUN if [ -f composer.lock ]; then \
        composer install --no-dev --optimize-autoloader; \
    else \
        composer update --no-dev --optimize-autoloader; \
    fi \
    && chown -R www-data:www-data /var/www/html

# 9. Expose port 8000
RUN sed -i 's/80/8000/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf
EXPOSE 8000

# 10. Perintah utama
CMD ["apache2-foreground"]
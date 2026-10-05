# Tandem — production image (PHP 8.2 + Apache)
# Used by Railway / Render / any Docker host. The database is external (Neon).

FROM php:8.2-apache

# PostgreSQL driver for PDO
RUN apt-get update \
 && apt-get install -y --no-install-recommends libpq-dev \
 && docker-php-ext-install pdo_pgsql \
 && rm -rf /var/lib/apt/lists/*

# Serve from public/ and let public/.htaccess route every request to index.php
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN a2enmod rewrite \
 && sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Allow up to 5 gallery images of 5 MB each in one form post
RUN { \
      echo 'upload_max_filesize = 6M'; \
      echo 'post_max_size = 32M'; \
      echo 'expose_php = Off'; \
    } > /usr/local/etc/php/conf.d/tandem.ini

COPY . /var/www/html
RUN mkdir -p /var/www/html/public/uploads \
 && chown -R www-data:www-data /var/www/html/public/uploads

# Railway (and most hosts) tell the app which port to listen on via $PORT
CMD sed -i "s/Listen 80/Listen ${PORT:-80}/" /etc/apache2/ports.conf \
 && sed -i "s/:80>/:${PORT:-80}>/" /etc/apache2/sites-available/000-default.conf \
 && apache2-foreground

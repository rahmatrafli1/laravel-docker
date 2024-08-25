FROM php:7.4-fpm
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git
RUN docker-php-ext-install pdo pdo_mysql zip
# Instal Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install
WORKDIR /var/www
COPY . /var/www
# Atur izin file
RUN chown -R www-data:www-data /var/www
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

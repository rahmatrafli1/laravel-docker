FROM php:7.4-fpm
# Set environment variables
ENV NVM_DIR /root/.nvm
ENV NODE_VERSION lts/hydrogen  # Specify the Node.js version you want
# Update Packages dan install
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    libzip-dev \
    vim
RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-install zip
WORKDIR /var/www
COPY . /var/www
# Instal Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# Install PHP dependencies
RUN composer install
# Salin .env.example dengan nama .env
RUN cp .env.example .env
# Generate application key
RUN php artisan key:generate
# Run the database migrations
RUN php artisan migrate
# Install NodeJS from nvm
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.0/install.sh | bash
RUN bash -c "source $NVM_DIR/nvm.sh && nvm install $NODE_VERSION && nvm use $NODE_VERSION && nvm alias default $NODE_VERSION"
# Atur izin file
RUN chown -R www-data:www-data /var/www
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

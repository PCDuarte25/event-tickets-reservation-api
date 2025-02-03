FROM php:8.2-apache

ARG USER_ID=1000
ARG GROUP_ID=1000

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql zip

# Altera o UID/GID do usuário www-data para corresponder ao host
RUN usermod -u ${USER_ID} www-data && \
    groupmod -g ${GROUP_ID} www-data

RUN a2enmod rewrite

COPY . /var/www/html

WORKDIR /var/www/html

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install

# Garante permissões adequadas (agora o www-data tem o mesmo UID/GID do host)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

COPY apache.conf /etc/apache2/sites-available/000-default.conf

FROM php:8.2.2-apache

ARG USER="arbeitszeugnisgenerator"
ARG UID=1000

ENV APACHE_DOCUMENT_ROOT="/var/www/html/public"

WORKDIR "/var/www/html"

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions gd zip opcache @composer

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
COPY ".docker/000-default.conf" "/etc/apache2/sites-available/000-default.conf"

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY "./.docker/prod/php-opcache.ini" "$PHP_INI_DIR/conf.d/php-opcache.ini"

RUN useradd --create-home --shell /bin/bash -U $USER -u $UID

COPY . "/var/www/html/"
RUN chown -R 1000:1000 "/var/www/html"

USER $USER

RUN composer install --optimize-autoloader --no-dev

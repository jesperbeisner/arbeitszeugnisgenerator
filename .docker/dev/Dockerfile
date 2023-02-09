FROM php:8.2.2-apache

ARG USER="arbeitszeugnisgenerator"
ARG UID=1000

ENV APACHE_DOCUMENT_ROOT="/var/www/html/public"

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions gd zip xdebug @composer

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

COPY ".docker/000-default.conf" "/etc/apache2/sites-available/000-default.conf"

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN useradd --create-home --shell /bin/bash -U $USER -u $UID

USER $USER

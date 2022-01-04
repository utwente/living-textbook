FROM php:8.0.14-fpm-alpine3.15
MAINTAINER Drienerlo Engineering Solutions <hallo@drenso.nl>

# Update image
RUN apk --no-cache upgrade

# Set timezone to Amsterdam
RUN printf "date.timezone = \"Europe/Amsterdam\"\n" >> `php -i | \
 grep php.ini | awk '{print $6'}`/php.ini

# Disable asserts
RUN printf "zend.assertions = -1\n" >> `php -i | grep php.ini | awk '{print $6'}`/php.ini

# ZIP extension
RUN apk add --no-cache unzip libzip-dev \
 && docker-php-ext-install zip

# Enable APCU
RUN apk add --no-cache --virtual .apcu-build-deps \
 autoconf automake make gcc g++ \
 && pecl install apcu \
 && docker-php-ext-enable apcu \
 && apk del .apcu-build-deps

# Opcache
RUN docker-php-ext-install opcache

# MySQL extension
RUN docker-php-ext-install pdo_mysql

# GD extension
RUN apk add --no-cache libpng-dev jpeg-dev gd-dev \
 && docker-php-ext-install gd

# Intl extenstion
RUN apk add --no-cache icu-dev \
 && docker-php-ext-install intl

# Sockets extenstion
RUN docker-php-ext-install sockets

# Xsl extension
RUN apk add --no-cache libxslt libxslt-dev \
 && docker-php-ext-install xsl

# Imagick extension
RUN apk add --no-cache --virtual .imagick-build-deps \
 autoconf automake make gcc g++ \
 && apk add --no-cache imagemagick-dev \
 && pecl install imagick-3.4.3 \
 && docker-php-ext-enable imagick \
 && apk del .imagick-build-deps

# Install composer globally
COPY install-composer.sh .
RUN sh install-composer.sh && rm install-composer.sh

WORKDIR /var/www/ltb
EXPOSE 9000

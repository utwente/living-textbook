FROM php:7.1-cli
MAINTAINER Drienerlo Engineering Solutions <hallo@drenso.nl>

# Install testing repo for newer git version & update repositories
RUN echo "APT::Default-Release \"jessie\";" > /etc/apt/apt.conf.d/99defaultrelease && \ 
    echo "deb http://deb.debian.org/debian testing main" >> /etc/apt/sources.list && \
    apt-get update && \
    apt-get upgrade -y

# Install wget
RUN apt-get install -y wget

# Update php settings
RUN printf "memory_limit = 512M\ndate.timezone = \"Europe/Amsterdam\"\n" >> `php -i | \
    grep php.ini | awk '{print $6'}`/php.ini

# Disable asserts
RUN printf "zend.assertions = -1\n" >> `php -i | grep php.ini | awk '{print $6'}`/php.ini

# Git (Composer)
RUN apt-get install -y git/testing

# php-zip/unzip (composer/memcached)
RUN apt-get install -y unzip zlib1g-dev libpcre3-dev && \
    docker-php-ext-install zip

# LDAP
RUN apt-get install -y libldap2-dev && \
    docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ && \
    docker-php-ext-install ldap

# MySQL extension
RUN docker-php-ext-install pdo pdo_mysql

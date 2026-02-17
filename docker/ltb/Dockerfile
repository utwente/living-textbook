FROM php:8.4.14-fpm-bookworm AS ltb_base
LABEL org.opencontainers.image.authors="Drienerlo Engineering Solutions <hallo@drenso.nl>"

# Update all dependencies, and install required ones
RUN export DEBIAN_FRONTEND=noninteractive \
    && apt-get update \
    && apt-get upgrade -y \
    # Install image dependencies
    && apt-get install -y \
    # PHP dependencies
    ## ZIP
      unzip zlib1g-dev libpcre3-dev libzip-dev \
    ## GD
      libpng-dev \
    ## Intl
      libicu-dev \
    ## XSL
      libxslt-dev \
    ## Imagick
      imagemagick libmagickwand-dev libmagickcore-dev \
    # Latex
      texlive-base texlive-latex-base texlive-latex-extra texlive-fonts-recommended texlive-fonts-extra poppler-utils \
    # Pandoc
      pandoc \
    # Python build dependencies \
# todo: Python sources
#      build-essential zlib1g-dev libncurses5-dev libgdbm-dev libnss3-dev libssl-dev libsqlite3-dev libreadline-dev libffi-dev curl libbz2-dev \
    # Wait for script
      netcat-openbsd \
    # Supervisor
      supervisor \
    # rsync
      rsync\
    # Cleanup apt
    && apt-get clean \
    && rm -rf /var/lib/apt/lists

# todo: Python sources
## Install python 3.8
#ARG PYTHON_VERSION=3.8.13
#RUN curl -o /tmp/python.tar.xz https://www.python.org/ftp/python/${PYTHON_VERSION}/Python-${PYTHON_VERSION}.tar.xz \
#    && tar -xf /tmp/python.tar.xz \
#    && mv Python-${PYTHON_VERSION} /opt/python${PYTHON_VERSION} \
#    && cd /opt/python${PYTHON_VERSION} \
#    && ./configure --enable-optimizations --enable-shared \
#    && make -j 8 \
#    && make altinstall \
#    && ldconfig /opt/python${PYTHON_VERSION}
#ENV PYTHON_PATH=/usr/local/bin/python3.8

# Set system timezone
RUN rm -f /etc/localtime \
    && ln -s /usr/share/zoneinfo/Europe/Amsterdam /etc/localtime \
	  && echo Europe/Amsterdam > /etc/timezone

# Set PHP timezone to Amsterdam
RUN printf "date.timezone = \"Europe/Amsterdam\"\n" >> `php -i | grep php.ini | awk '{print $6'}`/php.ini

# Disable asserts
RUN printf "zend.assertions = -1\n" >> `php -i | grep php.ini | awk '{print $6'}`/php.ini

# Install PHP extensions
RUN docker-php-ext-install zip
RUN docker-php-ext-install opcache
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install gd
RUN docker-php-ext-install intl
RUN docker-php-ext-install sockets
RUN docker-php-ext-install xsl
RUN docker-php-ext-install sysvsem
RUN pecl install apcu && docker-php-ext-enable apcu
RUN pecl install imagick && docker-php-ext-enable imagick

# Fix ulem package
RUN mkdir /usr/share/texmf/tex/latex/ulem \
    && (curl -s -f https://raw.githubusercontent.com/bobvandevijver/latex-bundle/v2.7.2/src/Resources/packages/ulem.sty \
      > /usr/share/texmf/tex/latex/ulem/ulem.sty) \
    && texhash

# Install composer globally
COPY ./docker/ltb/install-composer.sh .
RUN sh install-composer.sh && rm install-composer.sh

# Configure Imagick
RUN sed -ri 's/rights="(none)" pattern="PDF"/rights="read" pattern="PDF"/gi' /etc/ImageMagick-6/policy.xml

# Install wait-for script
RUN curl -s https://raw.githubusercontent.com/eficode/wait-for/master/wait-for -o /opt/wait-for \
    && chmod +x /opt/wait-for

# Set the default working directory
WORKDIR /usr/src/app

# Copy docker scripts
COPY docker docker

# Setup container configuration
RUN mv docker/ltb/docker-entry.sh /opt/docker-entry.sh \
    && chmod +x /opt/docker-entry.sh \
    && mv docker/ltb/supervisor/* /etc/supervisor/conf.d/ \
    && rm -rf docker

######################## Development image ########################
FROM ltb_base AS ltb_dev

# Install debug extension
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Set entry point
ENTRYPOINT ["/opt/docker-entry.sh"]

######################## Production php builder ########################
FROM ltb_base AS ltb_prod_php_build

# Install application
COPY . .

# Install php vendors and prepare python environment
ENV COMPOSER_NO_DEV=1
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN cp .secrets.json.dist .secrets.json \
    && composer install --no-scripts \
    && composer dump-autoload --classmap-authoritative \
    && composer install \
    && composer clearcache \
# todo: Python sources
#    && bin/console ltb:python:build \
    && rm -rf var \
    && rm .secrets.json

######################## Production assets builder ########################
FROM node:20.20.0-slim AS ltb_prod_assets_build

# Set the work dir
WORKDIR /usr/src/app

# Install application
COPY . .
COPY --from=ltb_prod_php_build /usr/src/app/assets/js/_fos_routes.json ./assets/js/_fos_routes.json
COPY --from=ltb_prod_php_build /usr/src/app/vendor/friendsofsymfony ./vendor/friendsofsymfony
COPY --from=ltb_prod_php_build /usr/src/app/vendor/studio-42 ./vendor/studio-42

# Install assets dependencies
RUN npm ci

# Build the assets
RUN npm run build

######################## Production image ########################
FROM ltb_prod_php_build AS ltb_prod

# Install assets
COPY --from=ltb_prod_assets_build /usr/src/app/public/build ./public/build
COPY --from=ltb_prod_assets_build /usr/src/app/public/email ./public/email

# Set some permissions
RUN mkdir -p var \
    && mkdir -p uploads \
    && chmod o+r -R public \
    && find public -type d -exec chmod o+x {} \+ \
    && chown -R www-data . \
    && chmod o+x uploads \
    && chmod u+rwx var

# Mark as production environment
RUN echo "APP_ENV=prod" > .env.local

# Set entry point
ENTRYPOINT ["/opt/docker-entry.sh"]

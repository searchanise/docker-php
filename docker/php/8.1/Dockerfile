FROM php:8.1-fpm
# Install general applications
RUN apt-get update && apt-get install -y --no-install-recommends \
    supervisor \
    ca-certificates \
    curl \
    git \
    ghostscript \
    mc \
    netcat \
    xz-utils \
    zip unzip \
    openssh-client \
    sudo \
    net-tools \
    mariadb-client \
    procps \
    telnet \
    wget \
    iputils-ping;

RUN set -eux; \
# Add checkpoint for clean after build
    savedAptMark="$(apt-mark showmanual)"; \
# Install applications for php build
    apt-get install -y --no-install-recommends \
    openssl \
    libxml2-dev \
    libonig-dev \
    libcurl4-gnutls-dev \
    libfreetype6-dev \
    libjpeg-dev \
    libmagickwand-dev \
    libpng-dev \
    libzip-dev \
    libenchant-2-dev \
    ; \
    docker-php-ext-configure gd \
    --with-freetype=/usr/include/ \
    --with-jpeg=/usr/include/ \
    ; \
    docker-php-ext-install -j "$(nproc)" \
    bcmath \
    enchant \
    exif \
    gd \
    intl \
    mysqli \
    pdo_mysql \
    soap \
    sockets \
    zip \
    pcntl \
    ; \
# https://pecl.php.net/package/imagick
    pecl install imagick-3.5.0 redis; \
    docker-php-ext-enable imagick redis; \
    rm -r /tmp/pear; \
# Reset apt-mark's "manual" list so that "purge --auto-remove" will remove all build dependencies
    apt-mark auto '.*' > /dev/null; \
    apt-mark manual $savedAptMark; \
    ldd "$(php -r 'echo ini_get("extension_dir");')"/*.so \
    | awk '/=>/ { print $3 }' \
    | sort -u \
    | xargs -r dpkg-query -S \
    | cut -d: -f1 \
    | sort -u \
    | xargs -rt apt-mark manual; \
# Revert to checkpoint
    apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
    rm -rf /var/lib/apt/lists/*

RUN ln -s /usr/local/bin/php /usr/bin/php

# Xdebug installization
RUN set -xe ; \
    pecl install xdebug; \
    docker-php-ext-enable xdebug;

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY image-files/ /
ENTRYPOINT docker-entrypoint.php

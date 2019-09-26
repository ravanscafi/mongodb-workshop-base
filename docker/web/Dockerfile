FROM php:7.3-fpm-alpine

ENV PHPIZE_DEPS \
  autoconf \
  dpkg-dev dpkg \
  file \
  g++ \
  gcc \
  libc-dev \
  make \
  pkgconf \
  re2c

RUN apk add --update --no-cache \
  git \
  libressl-dev

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
  && pecl install mongodb \
  && docker-php-ext-enable mongodb \
  && rm -rf tmp/* \
  && apk del --no-network .build-deps

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Running container as non-root user
RUN sed -i 's/user = www-data/; user = www-data/g' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/group = www-data/; group = www-data/g' /usr/local/etc/php-fpm.d/www.conf

USER www-data:www-data

ENV PATH=$PATH:/home/www-data/.composer/vendor/bin

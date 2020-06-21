FROM php:7.4-cli-alpine
RUN apk add --no-cache --update gettext-dev icu-dev oniguruma
RUN apk add --no-cache --virtual buildDeps autoconf automake gawk build-base oniguruma-dev
RUN pecl install xdebug-2.9.6
RUN docker-php-ext-install -j$(nproc) iconv intl mbstring
RUN docker-php-ext-enable sodium xdebug
RUN apk del buildDeps
RUN mkdir -p /app
VOLUME /app
WORKDIR /app

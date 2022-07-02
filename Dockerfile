FROM php:8.1.7-cli-alpine3.16

RUN apk add --no-cache --update gettext-dev gnu-libiconv icu-dev oniguruma
RUN apk add --no-cache --virtual buildDeps autoconf automake gawk build-base oniguruma-dev
RUN docker-php-ext-install -j$(nproc) gettext intl mbstring
RUN docker-php-ext-enable gettext intl mbstring sodium
RUN apk del buildDeps
RUN mkdir -p /app
VOLUME /app
WORKDIR /app

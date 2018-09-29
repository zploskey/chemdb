FROM php:5.6-cli

RUN mkdir /code
WORKDIR /code

RUN apt-get update && \
    apt-get install -y --no-install-recommends git zip unzip zlib1g-dev
RUN docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        zip

ADD . /code/

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_NO_INTERACTION 1
ENV COMPOSER_HOME /tmp
RUN curl --silent --show-error https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    composer --ansi --version  && \
    composer install

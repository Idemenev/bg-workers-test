FROM php:7.2
WORKDIR /var/www

RUN docker-php-ext-install bcmath \
    && touch workers.log \
    && chown 1000:1000 workers.log \
    && chmod 0777 workers.log

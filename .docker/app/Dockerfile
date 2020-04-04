FROM php:7.4-fpm

RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git zip unzip

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php composer-setup.php -- --filename=composer --install-dir=/usr/local/bin  \
 && rm composer-setup.php

RUN pecl install xdebug-2.9.4 \ && docker-php-ext-enable xdebug
RUN docker-php-ext-install pdo pdo_mysql

RUN echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_autostart=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_host=172.17.0.1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
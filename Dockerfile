FROM php:8.2-apache

RUN docker-php-ext-install pdo_mysql \
    && a2enmod rewrite headers

WORKDIR /var/www/html

COPY . /var/www/html
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY assets/ /var/www/html/public/assets/

EXPOSE 80

# Etapa 1: Composer (gera vendor/)
FROM composer:2 AS build
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress
COPY . .
RUN composer dump-autoload -o

# Etapa 2: Apache + PHP 8.2
FROM php:8.2-apache
RUN docker-php-ext-install pdo pdo_mysql
RUN a2enmod rewrite \
 && sed -ri 's#DocumentRoot /var/www/html#DocumentRoot /var/www/html/public#g' /etc/apache2/sites-available/000-default.conf \
 && sed -ri 's#<Directory /var/www/>#<Directory /var/www/html/public/>#g' /etc/apache2/apache2.conf \
 && sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf
# silencia aviso de ServerName
RUN echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf && a2enconf servername

WORKDIR /var/www/html
COPY --from=build /app /var/www/html
RUN chown -R www-data:www-data /var/www/html
EXPOSE 80

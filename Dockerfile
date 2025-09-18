# Etapa 1: instalar dependências PHP com Composer
FROM composer:2 AS build
WORKDIR /app

# instala deps só com composer.* (cache eficiente)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress

# copia o restante do projeto e otimiza autoload
COPY . .
RUN composer dump-autoload -o

# Etapa 2: servidor Apache + PHP
FROM php:8.2-apache

# extensões PHP (ajuste se seu app exigir outras)
RUN docker-php-ext-install pdo pdo_mysql

# habilita mod_rewrite e aponta DocumentRoot para /public
RUN a2enmod rewrite \
 && sed -ri 's#DocumentRoot /var/www/html#DocumentRoot /var/www/html/public#g' /etc/apache2/sites-available/000-default.conf \
 && sed -ri 's#<Directory /var/www/>#<Directory /var/www/html/public/>#g' /etc/apache2/apache2.conf \
 && sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

WORKDIR /var/www/html

# copia o app (inclui vendor da etapa build)
COPY --from=build /app /var/www/html
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

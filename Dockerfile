FROM php:8.2-apache
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN a2enmod rewrite && sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf
WORKDIR /var/www/html
COPY . /var/www/html
EXPOSE 80

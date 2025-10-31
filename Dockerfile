FROM php:8.2-apache

# Install Postgres (and MySQL if you still want both)
RUN apt-get update && apt-get install -y libpq-dev \
 && docker-php-ext-install pdo_pgsql pgsql \
 && docker-php-ext-install pdo_mysql mysqli \
 && a2enmod rewrite

# App files
WORKDIR /var/www/html
COPY . /var/www/html

# Use startup script to bind Apache to $PORT
COPY render-apache.sh /usr/local/bin/render-apache.sh
RUN chmod +x /usr/local/bin/render-apache.sh

CMD ["sh","-lc","/usr/local/bin/render-apache.sh"]

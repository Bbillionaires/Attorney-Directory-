FROM php:8.2-cli
RUN apt-get update && apt-get install -y libpq-dev \
 && docker-php-ext-install pdo_pgsql pgsql \
 && docker-php-ext-install pdo_mysql mysqli
WORKDIR /app
COPY . /app
CMD ["sh","-lc","./start.sh"]

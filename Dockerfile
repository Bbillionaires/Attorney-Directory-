FROM php:8.2-cli

# Install PostgreSQL client libs then PHP drivers
RUN apt-get update && apt-get install -y libpq-dev \
 && docker-php-ext-install pdo_pgsql pgsql \
 && docker-php-ext-install pdo_mysql mysqli

WORKDIR /app
COPY . /app

# Bind to Render's assigned $PORT
CMD ["sh", "-lc", "php -d display_errors=1 -d error_reporting=32767 -S 0.0.0.0:${PORT} -t /app"]

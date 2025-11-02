FROM php:8.2-cli

# install postgres headers for pdo_pgsql
RUN apt-get update && apt-get install -y libpq-dev \
 && docker-php-ext-install pdo_pgsql pgsql \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY . /app

# Use Legalform as docroot for the built-in server
# router.php is still at /app/router.php
CMD ["bash","-lc","php -d display_errors=1 -d error_reporting=32767 -S 0.0.0.0:${PORT} -t /app/Legalform /app/router.php"]

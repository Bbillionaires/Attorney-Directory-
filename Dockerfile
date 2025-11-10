FROM php:8.2-cli

# Install Postgres extensions for PDO
RUN apt-get update \
  && apt-get install -y libpq-dev \
  && docker-php-ext-install pdo_pgsql pgsql \
  && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY . /app

# IMPORTANT: run PHP's built-in server on port 10000 (what Render expects)
CMD ["php","-S","0.0.0.0:10000","router.php"]

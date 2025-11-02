# Dockerfile
FROM php:8.2-cli

# Install system deps + libpq headers for Postgres extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates git unzip libpq-dev \
  && rm -rf /var/lib/apt/lists/*

# Compile and enable Postgres extensions
RUN docker-php-ext-install pdo_pgsql pgsql

# Workdir and app files
WORKDIR /app
COPY . /app

# Render will inject $PORT; default helps local dev
ENV PORT=10000

# Start PHP built-in server with router
CMD ["bash","-lc","php -d display_errors=1 -d error_reporting=32767 -S 0.0.0.0:${PORT} -t /app /app/router.php"]

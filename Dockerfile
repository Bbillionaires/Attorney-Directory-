# Dockerfile
FROM php:8.2-cli

# system deps if you need them later (kept minimal)
RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates git unzip && rm -rf /var/lib/apt/lists/*

# postgres PDO drivers
RUN docker-php-ext-install pdo_pgsql pgsql

# put code in /app
WORKDIR /app
COPY . /app

# default port for local dev; Render will inject $PORT at runtime
ENV PORT=10000

# start PHP built-in server with our router
CMD ["bash","-lc","php -d display_errors=1 -d error_reporting=32767 -S 0.0.0.0:${PORT} -t /app /app/router.php"]

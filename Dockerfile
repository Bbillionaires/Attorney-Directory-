FROM php:8.2-cli

# system deps for PostgreSQL extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
 && rm -rf /var/lib/apt/lists/*

# enable pdo_pgsql + pgsql
RUN docker-php-ext-install pdo_pgsql pgsql

# put code in /app
WORKDIR /app
COPY . /app

# show some diagnostics on boot
ENV PORT=10000
RUN printf '%s\n' "OK from Dockerfile build" > /app/.docker_build_marker

# run PHP dev server with our router
CMD ["bash","-lc","php -d display_errors=1 -d error_reporting=32767 -S 0.0.0.0:${PORT} -t /app /app/router.php"]

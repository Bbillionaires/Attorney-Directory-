FROM php:8.2-cli

# Workdir and app files
WORKDIR /app
COPY . /app

# Install Postgres client libs and PHP extensions
RUN apt-get update \
 && apt-get install -y --no-install-recommends libpq-dev \
 && docker-php-ext-install pdo pdo_pgsql pgsql \
 && rm -rf /var/lib/apt/lists/*

# Render uses PORT; php -S must bind to it
ENV PORT=10000
EXPOSE 10000

# Start the PHP dev server with our router
CMD ["bash","-lc","php -d display_errors=1 -d error_reporting=32767 -S 0.0.0.0:${PORT} -t /app /app/router.php"]

FROM php:8.2-cli

# Install pg client libs so pdo_pgsql will compile
RUN apt-get update && apt-get install -y libpq-dev && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pdo_pgsql pgsql

# Set workdir and copy project
WORKDIR /app
COPY . /app

# Expose Render port (Render sets $PORT)
ENV PORT=10000

# Start the PHP dev server using our router
CMD ["bash","-lc","php -d display_errors=1 -d error_reporting=32767 -S 0.0.0.0:${PORT} -t /app /app/router.php"]

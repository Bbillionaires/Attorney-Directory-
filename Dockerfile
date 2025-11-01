# Use a slim PHP image
FROM php:8.2-cli

# Install needed extensions (pdo_pgsql)
RUN apt-get update && apt-get install -y \
    libpq-dev \
 && docker-php-ext-install pdo pdo_pgsql \
 && rm -rf /var/lib/apt/lists/*

# Workdir for app
WORKDIR /app

# Copy app code
COPY . /app

# Ensure your start script (if you use one) is executable
# If you DON'T use start.sh, you can delete the next two lines.
RUN chmod +x /app/start.sh || true

# Default start: php built-in server with router.php
# (Render provides $PORT)
CMD ["bash","-lc","php -d display_errors=1 -d error_reporting=32767 -S 0.0.0.0:${PORT} -t /app /app/router.php"]

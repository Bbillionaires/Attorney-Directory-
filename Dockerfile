FROM php:8.2-apache
RUN apt-get update && apt-get install -y libpq-dev \
 && docker-php-ext-install pdo pdo_pgsql \
 && rm -rf /var/lib/apt/lists/*
COPY . /var/www/html/
EXPOSE 80

# --- enable PostgreSQL (pgsql + PDO) inside the image ---
# Works for official php:* images (docker-php-ext-install) and generic Debian/Ubuntu (php-pgsql)
RUN set -eux; \
  if command -v docker-php-ext-install >/dev/null 2>&1; then \
    apt-get update && apt-get install -y --no-install-recommends libpq-dev && \
    docker-php-ext-install -j"$(nproc)" pdo_pgsql pgsql; \
    rm -rf /var/lib/apt/lists/*; \
  else \
    apt-get update && apt-get install -y --no-install-recommends php-pgsql && \
    rm -rf /var/lib/apt/lists/*; \
  fi

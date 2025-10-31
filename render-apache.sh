#!/usr/bin/env sh
set -eux

: "${PORT:=8080}"

# Make Apache listen on $PORT (not 80)
sed -ri "s/Listen 80/Listen 0.0.0.0:${PORT}/" /etc/apache2/ports.conf || true
sed -ri "s#<VirtualHost \*:80>#<VirtualHost *:${PORT}>#" /etc/apache2/sites-available/000-default.conf || true

# Optional: ensure mod_rewrite and DocumentRoot are good
a2enmod rewrite || true

# Show what we ended up with (useful in logs)
echo "=== /etc/apache2/ports.conf ==="
cat /etc/apache2/ports.conf || true
echo "=== /etc/apache2/sites-available/000-default.conf ==="
cat /etc/apache2/sites-available/000-default.conf || true

# Start Apache in foreground
exec apache2-foreground

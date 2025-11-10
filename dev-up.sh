#!/usr/bin/env bash
set -e

cd /workspaces/Attorney-Directory- || exit 1

# Load local secrets
if [ -f ./dev.env ]; then
  # shellcheck source=/dev/null
  source ./dev.env
fi

echo "�� Starting Attorney Directory local server..."
echo "Using STRIPE_SECRET_KEY=${STRIPE_SECRET_KEY:0:10}..."
echo "Using DATABASE_URL=${DATABASE_URL:0:60}..."

# Restart PHP server cleanly
pkill -f "php -S 127.0.0.1:8081" || true
php -S 127.0.0.1:8081 router.php > /tmp/php-server.log 2>&1 &

sleep 1
tail -n 10 /tmp/php-server.log
echo "✅ Visit http://127.0.0.1:8081/"

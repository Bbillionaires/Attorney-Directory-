#!/usr/bin/env bash
set -e

cd /workspaces/Attorney-Directory- || exit 1

# Load local secrets for this shell
if [ -f ./dev.env ]; then
  # shellcheck source=/dev/null
  source ./dev.env
else
  echo "dev.env not found – create it first."
  exit 1
fi

echo "Using STRIPE_SECRET_KEY=${STRIPE_SECRET_KEY:0:10}..."
echo "Using DATABASE_URL=${DATABASE_URL}"

# Restart PHP dev server
pkill -f "php -S" || true
php -S 127.0.0.1:8081 router.php > /tmp/php-server.log 2>&1 &

sleep 1

echo
echo "== db_health.php =="
curl -s "http://127.0.0.1:8081/db_health.php" || echo "db_health.php not reachable"

echo
echo "== home page (first lines) =="
curl -i "http://127.0.0.1:8081/" | head -n 15

echo
echo "Open in browser:"
echo "  http://127.0.0.1:8081/"
echo "  http://127.0.0.1:8081/template_checkout_demo.php"
echo "  http://127.0.0.1:8081/admin_payments.php"

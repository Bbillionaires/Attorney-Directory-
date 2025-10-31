#!/usr/bin/env sh
set -eux
echo "PORT=${PORT:-unset}"
php -v
echo "Modules:"
php -m
echo "Tree /app:"
ls -la /app | sed -n '1,200p'
echo "Starting PHP dev server..."
exec php -d display_errors=1 -d error_reporting=32767 -S 0.0.0.0:${PORT:-8080} -t /app /app/router.php

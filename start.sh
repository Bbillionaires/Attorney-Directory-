#!/usr/bin/env sh
set -eux
echo "PORT=$PORT"
php -v
php -m | sort | tr '\n' ' ' | sed 's/ \{1,\}/, /g'
echo
echo "Listing /app:"
ls -la /app
echo "Starting PHP server..."
exec php -d display_errors=1 -d error_reporting=32767 -S 0.0.0.0:${PORT} -t /app

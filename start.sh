#!/usr/bin/env sh
set -eux
: "${PORT:=8080}"
echo "PORT=$PORT"
php -v
exec php -d display_errors=1 -d error_reporting=32767 -S 0.0.0.0:${PORT} -t /app /app/router.php

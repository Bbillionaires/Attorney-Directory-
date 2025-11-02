<?php
// super-verbose router for PHP built-in server
$uri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$qs   = $_SERVER['QUERY_STRING'] ?? '';
$when = gmdate('c');
error_log("ROUTER hit: {$uri}?{$qs} at {$when}");

// Always serve live index for "/" (and /index and /index.php)
if ($uri === '/' || $uri === '/index' || $uri === '/index.php') {
    require __DIR__ . '/index.php';
    exit;
}

// if the request matches a real file (css/js/png/etc), let the server serve it
$real = __DIR__ . $uri;
if (is_file($real)) {
    return false;
}

// health endpoint
if ($uri === '/healthz.php' || $uri === '/healthz') {
    header('Content-Type: text/plain');
    echo "ok\n";
    exit;
}

// db test passthrough
if ($uri === '/dbtest.php' || $uri === '/dbtest') {
    require __DIR__ . '/dbtest.php';
    exit;
}

// Fallback: 404 with details
http_response_code(404);
header('Content-Type: text/plain');
echo "404 from router. URI=$uri\n";

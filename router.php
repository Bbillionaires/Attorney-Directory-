<?php
$uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
error_log("ROUTER hit: {$uri}");

if ($uri === '/' || $uri === '/index' || $uri === '/index.php') {
    require __DIR__ . '/index.php';
    exit;
}

$real = __DIR__ . $uri;
if (is_file($real)) {
    return false; // serve static assets
}

if ($uri === '/healthz' || $uri === '/healthz.php') {
    header('Content-Type: text/plain'); echo "ok\n"; exit;
}

if ($uri === '/dbtest' || $uri === '/dbtest.php') {
    require __DIR__ . '/dbtest.php'; exit;
}

http_response_code(404);
header('Content-Type: text/plain');
echo "404 from router. URI=$uri\n";

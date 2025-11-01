<?php
// Minimal, explicit router for PHP built-in server on Render

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
error_log("ROUTER hit: " . $uri);

// 1) Health check stays simple
if ($uri === '/healthz.php') {
  require __DIR__ . '/healthz.php';
  return true;
}

// 2) Home page: always serve LIVE index.php
if ($uri === '/' || $uri === '/index.php') {
  define('FROM_ROUTER', true);
  define('ROUTER_INDEX', 'live');
  require __DIR__ . '/index.php';
  return true;
}

// 3) If the request maps to a real file (css/js/img/php), let PHP serve it
$path = __DIR__ . $uri;
if (is_file($path)) {
  return false; // let the server handle it
}

// 4) Fallback: SPA-style — send everything else to LIVE index.php
define('FROM_ROUTER', true);
define('ROUTER_INDEX', 'fallback-live');
require __DIR__ . '/index.php';
return true;

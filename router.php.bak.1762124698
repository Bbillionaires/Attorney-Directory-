<?php
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$full = __DIR__ . $uri;

// 1) If the request maps to a real file, let PHP's dev server serve it.
if (is_file($full)) {
  return false;
}

// 2) Explicit small routes
if ($uri === '/healthz.php') { require __DIR__ . '/healthz.php'; return; }
if ($uri === '/dbtest.php')  { require __DIR__ . '/dbtest.php';  return; }
if ($uri === '/which.php')   { require __DIR__ . '/which.php';   return; }

// 3) Otherwise, serve the app
require __DIR__ . '/index.php';

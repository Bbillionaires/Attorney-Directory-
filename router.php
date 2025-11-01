<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');

/* Serve existing files directly */
$full = __DIR__ . $uri;
if ($uri !== '/' && file_exists($full) && !is_dir($full)) {
  return false; // let PHP's server serve the asset
}

/* Known lightweight routes (no DB) */
if ($uri === '/healthz.php') {
  require __DIR__ . '/healthz.php';
  exit;
}

/* TEMP: serve static index to avoid DB issues on / */
require __DIR__ . '/index.php';

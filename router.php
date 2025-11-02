<?php
$uri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $uri;

# Serve existing files (css, js, php test pages, etc.) directly
if ($uri !== '/' && is_file($file)) {
  return false;
}

# Otherwise serve the app home
require __DIR__ . '/index.php';

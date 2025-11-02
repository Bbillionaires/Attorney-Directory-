<?php
$docroot = __DIR__ . '/Legalform';
chdir($docroot);

$uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$path = realpath($docroot . $uri);

// Let PHP dev server serve actual files
if ($path && str_starts_with($path, $docroot) && is_file($path)) {
  return false;
}

// Everything else goes to your app’s index
require $docroot . '/index.php';

<?php
$docroot = __DIR__ . '/Legalform';
if (!is_dir($docroot) || !is_file($docroot.'/index.php')) {
  http_response_code(500);
  echo "Router docroot missing: $docroot\n";
  exit;
}
chdir($docroot);

$uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$path = realpath($docroot . $uri);

/* Let built-in server serve existing static files */
if ($path && str_starts_with($path, $docroot) && is_file($path)) {
  return false;
}

/* Everything else goes to your app */
require $docroot . '/index.php';

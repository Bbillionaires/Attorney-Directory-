<?php
// Minimal router for PHP dev server on Render
$uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$root = __DIR__;
$path = realpath($root . $uri);

// If a real file exists (css, js, images, etc.), let the server serve it.
if ($uri !== '/' && $path && str_starts_with($path, $root) && is_file($path)) {
  return false; // serve static file directly
}

// Known endpoints
switch ($uri) {
  case '/':
    require $root . '/index.php'; break;

  // diagnostics (optional)
  case '/which.php':
  case '/dbtest.php':
  case '/healthz.php':
    require $root . $uri; break;

  default:
    // Allow direct .php access if the file exists
    if (preg_match('/\.php$/', $uri) && $path && is_file($path)) {
      require $path; break;
    }
    http_response_code(404);
    echo "Not Found";
}

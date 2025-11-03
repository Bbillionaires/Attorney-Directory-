<?php
// Minimal router for PHP built-in server on Render

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$docroot = __DIR__;

// Let the PHP dev server serve real files (CSS/JS/images/fonts) directly.
if (preg_match('#\.(?:css|js|mjs|map|png|jpg|jpeg|gif|svg|ico|webp|woff2?|ttf|eot)$#i', $uri)) {
  return false;
}

// Route table: clean URLs → real PHP files
$routes = [
  ''            => '/index.php',
  '/'           => '/index.php',
  '/index.php'  => '/index.php',

  '/items'      => '/list.php',
  '/items/'     => '/list.php',

  '/add'        => '/add.php',
  '/add/'       => '/add.php',

  '/public'     => '/public_list.php',
  '/public/'    => '/public_list.php',

  '/healthz'    => '/healthz.php',
  '/healthz.php'=> '/healthz.php',
];

// If a route matches and file exists, serve it.
if (isset($routes[$uri]) && is_file($docroot . $routes[$uri])) {
  require $docroot . $routes[$uri];
  exit;
}

// If a direct file was requested and exists, serve it.
if (is_file($docroot . $uri)) {
  require $docroot . $uri;
  exit;
}

// 404
http_response_code(404);
echo "Not Found";

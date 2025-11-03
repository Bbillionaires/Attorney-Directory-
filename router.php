<?php
declare(strict_types=1);

$docroot = __DIR__;
$uriRaw  = $_SERVER['REQUEST_URI'] ?? '/';
$uriPath = parse_url($uriRaw, PHP_URL_PATH) ?? '/';
$uri     = rtrim($uriPath, '/');

// DEBUG route to confirm router is active
if ($uri === '/_router') {
  header('Content-Type: text/plain');
  echo "ROUTER OK\n";
  echo "URI: ", $uriRaw, "\n";
  echo "DOCROOT: ", $docroot, "\n";
  exit;
}

// Let PHP serve real static files directly (images, css, js, fonts, maps)
if (preg_match('#\.(?:css|js|mjs|map|png|jpg|jpeg|gif|svg|ico|webp|woff2?|ttf|eot)$#i', $uriPath)) {
  return false; // let built-in server handle
}

// If a direct file exists (e.g. /foo.php or /healthz.php), include it
if (is_file($docroot . $uriPath)) {
  require $docroot . $uriPath;
  exit;
}

// If /foo exists as /foo.php, serve that
if (is_file($docroot . $uriPath . '.php')) {
  require $docroot . $uriPath . '.php';
  exit;
}

// Pretty routes -> map to real php files
$map = [
  ''        => '/index.php',
  '/'       => '/index.php',
  '/items'  => '/list.php',
  '/add'    => '/add.php',
  '/public' => '/public_list.php',
];

if (isset($map[$uri])) {
  $target = $docroot . $map[$uri];
  if (is_file($target)) {
    require $target;
    exit;
  }
}

// Fallback: try index.php if present
if (is_file($docroot . '/index.php')) {
  require $docroot . '/index.php';
  exit;
}

// Final 404
http_response_code(404);
header('Content-Type: text/plain');
echo "Not Found\n";

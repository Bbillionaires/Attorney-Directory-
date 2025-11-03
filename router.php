<?php
$docroot = __DIR__;
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$uri = rtrim($uri, '/');  // normalize

// DEBUG route to confirm router is active
if ($uri === '/_router') {
  header('Content-Type: text/plain');
  echo "ROUTER OK\nURI: ", ($_SERVER['REQUEST_URI'] ?? ''), "\n";
  exit;
}

// Let PHP serve static files directly
if (preg_match('#\.(?:css|js|mjs|map|png|jpg|jpeg|gif|svg|ico|webp|woff2?|ttf|eot)$#i', $uri)) {
  return false;
}

// direct file e.g. /foo.php
if (is_file($docroot . $uri)) {
  require $docroot . $uri;
  exit;
}
// allow /foo -> /foo.php
if (is_file($docroot . $uri . '.php')) {
  require $docroot . $uri . '.php';
  exit;
}

// pretty routes
$map = [
  '/items'  => '/list.php',
  '/add'    => '/add.php',
  '/public' => '/public_list.php',
  ''        => '/index.php',
  '/'       => '/index.php',
];

foreach ($map as $base => $file) {
  if ($uri === $base || ($base && str_starts_with($uri, $base.'/'))) {
    $target = $docroot . $file;
    if (is_file($target)) {
      require $target;
      exit;
    }
  }
}

// fallback
if (is_file($docroot . '/index.php')) {
  require $docroot . '/index.php';
  exit;
}

http_response_code(404);
header('Content-Type: text/plain');
echo "Not Found\n";

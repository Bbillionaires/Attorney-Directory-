<?php
// Robust router for PHP built-in server on Render

$docroot = __DIR__;
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$uri = rtrim($uri, '/');  // normalize: '/items/' -> '/items'

// Let PHP serve static files directly
if (preg_match('#\.(?:css|js|mjs|map|png|jpg|jpeg|gif|svg|ico|webp|woff2?|ttf|eot)$#i', $uri)) {
  return false;
}

// Quick debug route (safe to leave)
if ($uri === '/_router') {
  header('Content-Type: text/plain');
  echo "ROUTER OK\nURI: ", ($_SERVER['REQUEST_URI'] ?? ''), "\n";
  exit;
}

// 1) Direct file hit? serve it.
if (is_file($docroot . $uri)) {
  require $docroot . $uri;
  exit;
}
// Also allow '/foo' -> '/foo.php' if file exists
if (is_file($docroot . $uri . '.php')) {
  require $docroot . $uri . '.php';
  exit;
}

// 2) Pretty routes → actual PHP files (prefix matches allowed)
$map = [
  '/items'  => '/list.php',
  '/add'    => '/add.php',
  '/public' => '/public_list.php',
  '/'       => '/index.php',   // normalized '/' became '' → handle below
  ''        => '/index.php',
];

// If the URI equals or starts with a known key, serve mapped file
foreach ($map as $base => $file) {
  if ($uri === $base || ($base && str_starts_with($uri, $base.'/'))) {
    $target = $docroot . $file;
    if (is_file($target)) {
      require $target;
      exit;
    }
  }
}

// Final fallback to homepage
if (is_file($docroot . '/index.php')) {
  require $docroot . '/index.php';
  exit;
}

// 404
http_response_code(404);
header('Content-Type: text/plain');
echo "Not Found\n";

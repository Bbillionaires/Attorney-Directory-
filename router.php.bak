<?php
// Minimal, safe router for PHP built-in server on Render.
// Logs each request and serves a static home to avoid DB on "/".
$u = $_SERVER['REQUEST_URI'] ?? '';
error_log("ROUTER hit: " . $u);

// Let the dev server serve files that physically exist (assets, php files)
$path = parse_url($u, PHP_URL_PATH);
$full = __DIR__ . $path;
if ($path !== '/' && $path && file_exists($full)) {
  return false; // hand off to built-in server
}

// Static landing page while we stabilize DB:
if ($path === '/' || $path === '/index.php') {
  require __DIR__ . '/index.php';
  exit;
}

// Fallback: if a .php was requested that doesn't exist, 404 cleanly
http_response_code(404);
header('Content-Type: text/plain');
echo "Not found: {$path}\n";

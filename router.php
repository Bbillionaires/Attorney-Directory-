<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
if ($path === '/' || $path === '') { require __DIR__ . '/index.php'; return; }
$file = __DIR__ . $path;
if (is_file($file)) { return false; }  // let PHP dev server serve static files
http_response_code(404);
echo "Not found: $path";

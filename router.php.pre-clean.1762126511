<?php
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$full = __DIR__ . $uri;
if (is_file($full)) { return false; } // let built-in server serve actual files

if ($uri === '/healthz.php') { require __DIR__.'/healthz.php'; return; }
if ($uri === '/dbtest.php')  { require __DIR__.'/dbtest.php';  return; }
if ($uri === '/which.php')   { require __DIR__.'/which.php';   return; }

require __DIR__.'/index.php';

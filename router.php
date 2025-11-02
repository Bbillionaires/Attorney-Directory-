<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
error_log("ROUTER hit: $uri");

/* Let built-in server serve real files (assets/css/js/images) */
$full = __DIR__ . $uri;
if ($uri !== '/' && file_exists($full)) { return false; }

/* Health & debug shortcuts */
if ($uri === '/healthz' || $uri === '/healthz.php') { require __DIR__ . '/healthz.php'; exit; }
if ($uri === '/dbtest.php') { require __DIR__ . '/dbtest.php'; exit; }
if ($uri === '/which.php')  { require __DIR__ . '/which.php';  exit; }

/* Default: your real site */
require __DIR__ . '/index.php';

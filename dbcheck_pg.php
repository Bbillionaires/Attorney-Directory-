<?php
header('Content-Type: text/plain');

$url = getenv('DATABASE_URL');
if (!$url) { http_response_code(500); exit("No DATABASE_URL\n"); }

$parts = parse_url($url);
if (!$parts || !isset($parts['host'], $parts['path'])) {
  http_response_code(500); exit("Bad DATABASE_URL format: $url\n");
}
$host = $parts['host'];
$port = $parts['port'] ?? 5432;
$db   = ltrim($parts['path'], '/');
$user = $parts['user'] ?? '';
$pass = isset($parts['pass']) ? $parts['pass'] : '';

$dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";

try {
  $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
  echo "DB_OK\n";
  echo $pdo->query("select version()")->fetchColumn() . "\n";
} catch (Throwable $e) {
  http_response_code(500);
  echo "DB_ERROR: " . $e->getMessage() . "\n";
  echo "DSN_HOST=$host PORT=$port DB=$db USER=$user\n";
}

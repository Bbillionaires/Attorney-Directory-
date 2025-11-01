<?php
// Show errors in logs
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = getenv('DB_HOST') ?: '';
$port = getenv('DB_PORT') ?: '5432';
$db   = getenv('DB_NAME') ?: '';
$user = getenv('DB_USER') ?: '';
$pass = getenv('DB_PASS') ?: '';
$sslmode = getenv('DB_SSLMODE') ?: 'require'; // Render Postgres usually needs SSL

// Fallback: if DATABASE_URL is present, parse it
if (!$host && ($url = getenv('DATABASE_URL'))) {
  $parts = parse_url($url);
  $host  = $parts['host'] ?? $host;
  $port  = $parts['port'] ?? $port;
  $user  = $parts['user'] ?? $user;
  $pass  = $parts['pass'] ?? $pass;
  $db    = ltrim($parts['path'] ?? '', '/');
  // Detect sslmode= in query if present
  if (!empty($parts['query'])) {
    parse_str($parts['query'], $q);
    if (!empty($q['sslmode'])) $sslmode = $q['sslmode'];
  }
}

$dsn = "pgsql:host={$host};port={$port};dbname={$db};sslmode={$sslmode}";

try {
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
  ]);
} catch (Throwable $e) {
  $GLOBALS['DB_ERROR'] = $e->getMessage();
  $pdo = null;
}

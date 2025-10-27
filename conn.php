<?php
$pgUrl = getenv('DATABASE_URL');

if ($pgUrl) {
  $parts = parse_url($pgUrl);
  $host = $parts['host'] ?? 'localhost';
  $port = $parts['port'] ?? 5432;
  $user = $parts['user'] ?? '';
  $pass = $parts['pass'] ?? '';
  $name = ltrim($parts['path'] ?? '/postgres', '/');
  $ssl  = (strpos($pgUrl, 'sslmode=') !== false) ? 'require' : '';
} else {
  $host = getenv('DB_HOST') ?: 'localhost';
  $port = getenv('DB_PORT') ?: '5432';
  $user = getenv('DB_USER') ?: '';
  $pass = getenv('DB_PASS') ?: '';
  $name = getenv('DB_NAME') ?: '';
  $ssl  = getenv('DB_SSLMODE') ?: 'require';
}

try {
  $dsn = "pgsql:host=$host;port=$port;dbname=$name";
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (Throwable $e) {
  error_log("DB connect failed: " . $e->getMessage());
  $pdo = null;
}

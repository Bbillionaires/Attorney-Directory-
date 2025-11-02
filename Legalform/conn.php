<?php
$envUrl = getenv('DATABASE_URL'); // e.g. postgres://user:pass@host:5432/db?sslmode=require
if (!$envUrl) {
  $host = getenv('DB_HOST') ?: '127.0.0.1';
  $port = getenv('DB_PORT') ?: '5432';
  $name = getenv('DB_NAME') ?: 'postgres';
  $user = getenv('DB_USER') ?: 'postgres';
  $pass = getenv('DB_PASS') ?: '';
  $dsn  = "pgsql:host=$host;port=$port;dbname=$name;sslmode=require";
} else {
  $parts = parse_url($envUrl);
  $user  = urldecode($parts['user'] ?? '');
  $pass  = urldecode($parts['pass'] ?? '');
  $host  = $parts['host'] ?? '127.0.0.1';
  $port  = $parts['port'] ?? '5432';
  $name  = ltrim($parts['path'] ?? '/postgres','/');
  parse_str($parts['query'] ?? '', $qs);
  $ssl   = $qs['sslmode'] ?? 'require';
  $dsn   = "pgsql:host=$host;port=$port;dbname=$name;sslmode=$ssl";
}
try {
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => true,
  ]);
} catch (Throwable $e) {
  $GLOBALS['DB_ERROR'] = $e->getMessage();
  $pdo = null;
}

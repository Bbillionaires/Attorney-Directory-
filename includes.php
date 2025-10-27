<?php
$u = parse_url(getenv('DATABASE_URL') ?: '');
$dsn = sprintf(
  'pgsql:host=%s;port=%s;dbname=%s;sslmode=require',
  $u['host'] ?? '', $u['port'] ?? 5432, ltrim($u['path'] ?? '/', '/')
);
try {
  $pdo = new PDO($dsn, $u['user'] ?? '', $u['pass'] ?? '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (Throwable $e) {
  die('Database connection error: '.$e->getMessage());
}

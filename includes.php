<?php
$u = parse_url(getenv('DATABASE_URL') ?: '');
if (!$u) die('DATABASE_URL missing');
$dsn = sprintf(
  'pgsql:host=%s;port=%s;dbname=%s;sslmode=require',
  $u['host'], $u['port'] ?? 5432, ltrim($u['path'] ?? '/', '/')
);

try {
  $pdo = new PDO($dsn, $u['user'], $u['pass'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
<?php /* removed noisy DB banner */ ?>
} catch (Throwable $e) {
  die('❌ DB connect failed: '.$e->getMessage());
}
?>

<?php
$pdo = null;
try {
  $url = getenv('DATABASE_URL');
  if (!$url) { throw new RuntimeException('Missing DATABASE_URL'); }
  $url = preg_replace('#^postgres(ql)?://#','pgsql://',$url);
  $p = parse_url($url);
  if (!$p) { throw new RuntimeException('Bad DATABASE_URL'); }
  $user = $p['user'] ?? null;
  $pass = $p['pass'] ?? null;
  $host = $p['host'] ?? null;
  $port = $p['port'] ?? 5432;
  $db   = ltrim($p['path'] ?? '', '/');
  if (!$user || !$pass || !$host || !$db) {
    throw new RuntimeException('Incomplete DATABASE_URL');
  }
  $dsn = "pgsql:host={$host};port={$port};dbname={$db};sslmode=require";
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ]);
} catch (Throwable $e) {
  $GLOBALS['DB_ERROR'] = $e->getMessage();
}

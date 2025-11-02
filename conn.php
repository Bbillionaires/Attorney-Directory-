<?php
$pdo = null;
function pg_dsn(): array {
  $u = getenv('DATABASE_URL');
  if ($u) {
    $p = parse_url($u);
    $user = isset($p['user']) ? urldecode($p['user']) : '';
    $pass = isset($p['pass']) ? urldecode($p['pass']) : '';
    $host = $p['host'] ?? '127.0.0.1';
    $port = $p['port'] ?? '5432';
    $name = ltrim($p['path'] ?? '/postgres','/');
    // Always enforce a known-good sslmode
    $dsn = "pgsql:host={$host};port={$port};dbname={$name};sslmode=require";
    return [$dsn,$user,$pass];
  }
  $host = getenv('DB_HOST') ?: '127.0.0.1';
  $port = getenv('DB_PORT') ?: '5432';
  $name = getenv('DB_NAME') ?: 'postgres';
  $user = getenv('DB_USER') ?: 'postgres';
  $pass = getenv('DB_PASS') ?: '';
  $dsn  = "pgsql:host={$host};port={$port};dbname={$name};sslmode=require";
  return [$dsn,$user,$pass];
}
try {
  [$dsn,$user,$pass] = pg_dsn();
  $pdo = new PDO($dsn,$user,$pass,[
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES=>true,
  ]);
} catch (Throwable $e) {
  $GLOBALS['DB_ERROR'] = $e->getMessage();
  $pdo = null;
}

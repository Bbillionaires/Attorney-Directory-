<?php
// Robust, idempotent Postgres connector for Render-style DATABASE_URL
// Creates $GLOBALS['pdo'] exactly once. Never echoes output.

declare(strict_types=1);

if (!function_exists('pg_dsn')) {
  function pg_dsn(): array {
    $envUrl = getenv('DATABASE_URL') ?: '';
    if ($envUrl === '') {
      // Legacy fallback (only if present)
      $host = getenv('DB_HOST') ?: '127.0.0.1';
      $port = getenv('DB_PORT') ?: '5432';
      $name = getenv('DB_NAME') ?: 'postgres';
      $user = getenv('DB_USER') ?: 'postgres';
      $pass = getenv('DB_PASS') ?: '';
      $dsn  = "pgsql:host={$host};port={$port};dbname={$name};sslmode=require";
      return [$dsn, $user, $pass];
    }
    $parts = parse_url($envUrl);
    $user  = urldecode($parts['user'] ?? '');
    $pass  = urldecode($parts['pass'] ?? '');
    $host  = $parts['host'] ?? '127.0.0.1';
    $port  = $parts['port'] ?? '5432';
    $name  = ltrim($parts['path'] ?? '/postgres','/');
    parse_str($parts['query'] ?? '', $qs);
    $ssl   = ($qs['sslmode'] ?? 'require');
    // normalize bad values like "require_" to "require"
    if (!in_array($ssl, ['disable','allow','prefer','require','verify-ca','verify-full'], true)) {
      $ssl = 'require';
    }
    $dsn = "pgsql:host={$host};port={$port};dbname={$name};sslmode={$ssl}";
    return [$dsn, $user, $pass];
  }
}

if (!isset($GLOBALS['pdo']) || !($GLOBALS['pdo'] instanceof PDO)) {
  try {
    [$dsn, $user, $pass] = pg_dsn();
    $GLOBALS['pdo'] = new PDO($dsn, $user, $pass, [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES   => true,
    ]);
  } catch (Throwable $e) {
    $GLOBALS['DB_ERROR'] = $e->getMessage();
    $GLOBALS['pdo'] = null;
  }
}

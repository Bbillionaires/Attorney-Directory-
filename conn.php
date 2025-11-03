<?php
// Hard guard so this file can be required multiple times with no redeclare fatals
if (defined('APP_CONN_LOADED')) return;
define('APP_CONN_LOADED', true);

// ---- DSN builder made idempotent ----
if (!function_exists('pg_dsn')) {
  function pg_dsn(): array {
    $envUrl = getenv('DATABASE_URL');
    if ($envUrl) {
      $parts = parse_url($envUrl);
      $user  = urldecode($parts['user'] ?? '');
      $pass  = urldecode($parts['pass'] ?? '');
      $host  = $parts['host'] ?? '127.0.0.1';
      $port  = $parts['port'] ?? '5432';
      $name  = ltrim($parts['path'] ?? '/postgres', '/');
      parse_str($parts['query'] ?? '', $qs);
      $ssl   = ($qs['sslmode'] ?? 'require');
      if ($ssl === 'require_') $ssl = 'require'; // sanitize if it was mistyped
      $dsn   = "pgsql:host=$host;port=$port;dbname=$name;sslmode=$ssl";
      return [$dsn, $user, $pass];
    }
    // legacy envs fallback
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: '5432';
    $name = getenv('DB_NAME') ?: 'postgres';
    $user = getenv('DB_USER') ?: 'postgres';
    $pass = getenv('DB_PASS') ?: '';
    $dsn  = "pgsql:host=$host;port=$port;dbname=$name;sslmode=require";
    return [$dsn, $user, $pass];
  }
}

// ---- Create $pdo once, safely ----
if (!isset($pdo) || !($pdo instanceof PDO)) {
  try {
    [$dsn, $user, $pass] = pg_dsn();
    $pdo = new PDO($dsn, $user, $pass, [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES   => true,
    ]);
  } catch (Throwable $e) {
    $GLOBALS['DB_ERROR'] = $e->getMessage();
    $pdo = null;
  }
}

<?php
// ===== Parse DATABASE_URL (Render/Heroku style) =====
$envUrl = getenv('DATABASE_URL'); // e.g. postgres://user:pass@host:5432/db?sslmode=require
if (!$envUrl) {
  // Fallback to legacy envs if still present
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

/**
 * Minimal mysql_* compatibility so legacy code can run on PDO+Postgres.
 * (Bridge only; we can migrate to pure PDO later.)
 */

if (!function_exists('mysql_query')) {
  function mysql_query($sql) {
    global $pdo;
    if (!$pdo) { trigger_error("No DB connection", E_USER_WARNING); return false; }
    try {
      if (preg_match('/^\s*(SELECT|SHOW|WITH)\b/i', $sql)) {
        return $pdo->query($sql);
      } else {
        $pdo->exec($sql);
        return true;
      }
    } catch (Throwable $e) {
      $GLOBALS['DB_ERROR'] = $e->getMessage();
      return false;
    }
  }
}

if (!function_exists('mysql_fetch_assoc')) {
  function mysql_fetch_assoc($stmt) {
    if ($stmt instanceof PDOStatement) return $stmt->fetch(PDO::FETCH_ASSOC);
    return false;
  }
}

if (!function_exists('mysql_fetch_array')) {
  function mysql_fetch_array($stmt) {
    if ($stmt instanceof PDOStatement) return $stmt->fetch(PDO::FETCH_ASSOC);
    return false;
  }
}

if (!function_exists('mysql_real_escape_string')) {
  function mysql_real_escape_string($s) {
    global $pdo;
    if (!$pdo) return addslashes($s);
    $q = $pdo->quote($s);
    return substr($q, 1, -1); // strip quotes added by PDO::quote
  }
}

if (!function_exists('mysql_insert_id')) {
  function mysql_insert_id() {
    global $pdo;
    if (!$pdo) return 0;
    try { return (int)$pdo->lastInsertId(); } catch (Throwable $e) { return 0; }
  }
}

if (!function_exists('mysql_error')) {
  function mysql_error() {
    return $GLOBALS['DB_ERROR'] ?? '';
  }
}

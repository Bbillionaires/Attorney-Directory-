<?php
header('Content-Type: text/plain');
require_once __DIR__ . '/includes.php';
$pdo = pdo();
if (!($pdo instanceof PDO)) {
  http_response_code(503);
  echo "DB_ERROR: " . ($GLOBALS['DB_ERROR'] ?? 'no-connection') . "\n";
  exit;
}
try {
  $u = $pdo->query('select current_user as u')->fetch()['u'] ?? '?';
  $v = $pdo->query('select version() as v')->fetch()['v'] ?? '?';
  $d = $pdo->query('select current_database() as d')->fetch()['d'] ?? '?';
  echo "OK user={$u} db={$d}\n{$v}\n";
} catch (Throwable $e) {
  http_response_code(500);
  echo "QUERY_ERROR: " . $e->getMessage() . "\n";
}

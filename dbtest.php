<?php
require __DIR__ . '/conn.php';
header('Content-Type: text/plain');
if (!$pdo) {
  http_response_code(503);
  echo "DB_ERROR: " . ($GLOBALS['DB_ERROR'] ?? 'unknown') . "\n";
  exit;
}
try {
  $v = $pdo->query("select version() as v")->fetch()['v'] ?? 'unknown';
  echo "OK: connected\n{$v}\n";
} catch (Throwable $e) {
  http_response_code(500);
  echo "QUERY_ERROR: " . $e->getMessage() . "\n";
}

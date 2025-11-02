<?php
require __DIR__ . '/conn.php';
header('Content-Type: text/plain');

if (!isset($pdo) || !$pdo) {
  http_response_code(503);
  echo "DB_ERROR: " . ($GLOBALS['DB_ERROR'] ?? 'unknown') . "\n";
  exit;
}

try {
  $u = $pdo->query("select current_user")->fetchColumn();
  $d = $pdo->query("select current_database()")->fetchColumn();
  $v = $pdo->query("select version()")->fetchColumn();
  echo "OK user=$u db=$d version=$v\n";
} catch (Throwable $e) {
  http_response_code(500);
  echo "QUERY_ERROR: " . $e->getMessage() . "\n";
}

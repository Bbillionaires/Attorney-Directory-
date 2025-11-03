<?php require_once __DIR__ . '/../conn.php'; ?>
<?php
require __DIR__ . '/conn.php';
header('Content-Type: text/plain');
if (!$pdo) {
  http_response_code(503);
  echo "DB_ERROR: " . ($GLOBALS['DB_ERROR'] ?? 'unknown') . "\n";
  exit;
}
$v = $pdo->query("select version()")->fetchColumn();
echo "OK DB\nversion: $v\n";

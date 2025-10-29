<?php
require __DIR__.'/conn.php';
header('Content-Type: text/plain');
if (!$pdo) { echo "NO PDO instance"; exit; }
try {
  $v = $pdo->query('select version()')->fetchColumn();
  echo "Connected! Server version: $v";
} catch (Throwable $e) {
  echo "Connect/query error: ", $e->getMessage(), "";
}

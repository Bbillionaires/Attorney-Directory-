<?php
// Simple helpers using global $pdo (from conn.php)
function db_all(string $sql, array $params = []): array {
  global $pdo;
  $st = $pdo->prepare($sql);
  $st->execute($params);
  return $st->fetchAll(PDO::FETCH_ASSOC);
}
function db_one(string $sql, array $params = []): ?array {
  $rows = db_all($sql, $params);
  return $rows[0] ?? null;
}

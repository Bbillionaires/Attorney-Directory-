<?php
// Graceful DB connection for Render (works even if env vars are missing)
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$name = getenv('DB_NAME');

if (!$host || !$user || !$name) {
  // No DB configured yet; allow pages to load without DB.
  $pdo = null;
  return;
}

$dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";

try {
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (PDOException $e) {
  http_response_code(500);
  echo "Database connection failed: " . htmlspecialchars($e->getMessage());
  exit;
}

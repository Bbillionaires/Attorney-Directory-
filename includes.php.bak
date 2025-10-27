<?php
// Read DB creds from Render env vars
$host = getenv('DB_HOST');
$port = getenv('DB_PORT') ?: '5432';
$name = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');

// Force SSL for Render PG
$dsn = "pgsql:host={$host};port={$port};dbname={$name};sslmode=require";

try {
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (Throwable $e) {
  error_log("DB connect failed: ".$e->getMessage());
  http_response_code(500);
  echo "Database connection error.";
  exit;
}

// --- Lightweight shims to keep old code working ---
function mysql_query($sql){ global $pdo; return $pdo->query($sql); }
function mysql_fetch_array($stmt){ return $stmt->fetch(); }
function mysql_real_escape_string($s){ global $pdo; return substr((string)$pdo->quote($s),1,-1); }
?>

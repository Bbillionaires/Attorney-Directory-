<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$socket = getenv('HOME') . '/mysql.sock';
$dsn    = "mysql:unix_socket=$socket;dbname=attorneydb;charset=utf8mb4";
$user   = 'root';
$pass   = '';

try {
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (PDOException $e) {
  die('DB connect failed: ' . $e->getMessage());
}
?>

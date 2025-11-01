<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = getenv('DB_HOST');   // e.g. your MySQL host
$db   = getenv('DB_NAME');   // e.g. attorneydb
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (PDOException $e) {
  die('DB connect failed: ' . $e->getMessage());
}

<?php
declare(strict_types=1);

$dsn = 'pgsql:host=dpg-d3vl0oer433s73cvgcq0-a.oregon-postgres.render.com;port=5432;dbname=attorneydb;sslmode=require';
$user = 'attorneydirectory';
$pass = 'EOEVxdVxunGCo7OsJxr0wFNizAiSamEZ';

$pdo = new PDO($dsn, $user, $pass, [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => false,
]);

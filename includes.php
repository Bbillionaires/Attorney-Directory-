<?php
declare(strict_types=1);
require_once __DIR__ . '/conn.php';

if (!function_exists('pdo')) {
  function pdo(): ?PDO {
    return ($GLOBALS['pdo'] ?? null) instanceof PDO ? $GLOBALS['pdo'] : null;
  }
}

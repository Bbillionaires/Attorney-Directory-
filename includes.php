<?php
error_reporting(E_ALL); ini_set('display_errors', 1);

if (!isset($GLOBALS['__CONN_LOADED__'])) {
  require_once __DIR__ . '/conn.php';   // defines $pdo
  $GLOBALS['__CONN_LOADED__'] = true;
}

function pdo_or_null() {
  return isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO ? $GLOBALS['pdo'] : null;
}

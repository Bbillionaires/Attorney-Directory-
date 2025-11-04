<?php
error_reporting(E_ALL); ini_set('display_errors', 1);

if (!isset($GLOBALS['__CONN_LOADED__'])) {
  require_once __DIR__ . '/conn.php';   // defines $pdo
  $GLOBALS['__CONN_LOADED__'] = true;
}

function pdo_or_null() {
  return isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO ? $GLOBALS['pdo'] : null;
}

<?php
// --- Admin session helpers ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('is_admin')) {
    function is_admin(): bool {
        return !empty($_SESSION['is_admin']);
    }
}

if (!function_exists('require_admin')) {
    function require_admin(): void {
        if (empty($_SESSION['is_admin'])) {
            header('Location: /admin');
            exit;
        }
    }
}

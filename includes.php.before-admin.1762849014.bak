<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

/**
 * Helper used by index.php and other pages.
 * Returns a PDO or null if the DB is unavailable.
 */
function pdo_or_null(): ?PDO {
    try {
        return getPDO();
    } catch (Throwable $e) {
        error_log('pdo_or_null error: ' . $e->getMessage());
        return null;
    }
}

/**
 * Very simple admin guard.
 * TODO: replace with real authentication.
 * For now it just allows access so /add.php and other admin pages work.
 */
function require_admin(): void {
    // You can add real auth (password, login, etc.) later.
    return;
}

<?php
declare(strict_types=1);

// Ensure session exists (some pages may rely on it)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Single source of truth for DB: db.php
require_once __DIR__ . '/db.php';

/**
 * Always returns a PDO or throws if connection fails.
 * Use this if you *must* have a DB (e.g., admin scripts).
 */
function pdo(): PDO
{
    return getPDO();
}

/**
 * Returns a PDO or null if the DB is not available.
 * This is what index.php uses.
 */
function pdo_or_null(): ?PDO
{
    try {
        return getPDO();
    } catch (Throwable $e) {
        // Log the real error for debugging, but don't break the page
        error_log('pdo_or_null DB error: ' . $e->getMessage());
        return null;
    }
}

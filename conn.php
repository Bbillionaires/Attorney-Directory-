<?php
// Robust PostgreSQL connection that supports Render's DATABASE_URL.
// Leaves $pdo defined (PDO or null) and sets $GLOBALS['DB_ERROR'] on failure.

$pdo = null;

function build_pg_dsn_from_env(): array {
    $envUrl = getenv('DATABASE_URL'); // e.g. postgresql://user:pass@host:5432/db?sslmode=require
    if ($envUrl) {
        // parse_url works for postgres:// and postgresql://
        $parts = parse_url($envUrl);
        $user  = isset($parts['user']) ? urldecode($parts['user']) : '';
        $pass  = isset($parts['pass']) ? urldecode($parts['pass']) : '';
        $host  = $parts['host'] ?? '127.0.0.1';
        $port  = $parts['port'] ?? '5432';
        $name  = ltrim($parts['path'] ?? '/postgres', '/');
        parse_str($parts['query'] ?? '', $qs);
        $ssl   = $qs['sslmode'] ?? 'require';
        $dsn   = "pgsql:host={$host};port={$port};dbname={$name};sslmode={$ssl}";
        return [$dsn, $user, $pass];
    }

    // Legacy fallbacks (only used if DATABASE_URL is missing)
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: '5432';
    $name = getenv('DB_NAME') ?: 'postgres';
    $user = getenv('DB_USER') ?: 'postgres';
    $pass = getenv('DB_PASS') ?: '';
    $dsn  = "pgsql:host={$host};port={$port};dbname={$name};sslmode=require";
    return [$dsn, $user, $pass];
}

try {
    [$dsn, $user, $pass] = build_pg_dsn_from_env();
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => true,
    ]);
} catch (Throwable $e) {
    $GLOBALS['DB_ERROR'] = $e->getMessage();
    $pdo = null; // ensure the symbol exists
}

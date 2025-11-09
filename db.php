<?php

function getPDO(): PDO
{
    // Single source of truth for DB connection.
    // No env var, so this will be stable as long as these credentials stay valid.
    $dbUrl = 'postgresql://attorneydirectory:EOEVxdVxunGCo7OsJxr0wFNizAiSamEZ@dpg-d3vl0oer433s73cvgcq0-a.oregon-postgres.render.com/attorneydb?sslmode=require';

    $parts = parse_url($dbUrl);
    if ($parts === false) {
        throw new RuntimeException('Invalid DB URL');
    }

    $host   = $parts['host'] ?? '127.0.0.1';
    $port   = $parts['port'] ?? 5432;
    $user   = $parts['user'] ?? '';
    $pass   = $parts['pass'] ?? '';
    $dbname = isset($parts['path']) ? ltrim($parts['path'], '/') : '';

    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require";

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}

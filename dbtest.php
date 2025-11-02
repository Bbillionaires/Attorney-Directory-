<?php
require __DIR__ . '/conn.php';
header('Content-Type: text/plain');

if (!$pdo) {
    http_response_code(503);
    echo "DB_ERROR: " . ($GLOBALS['DB_ERROR'] ?? 'unknown') . PHP_EOL;
    exit;
}

try {
    $ver = $pdo->query("select version() v")->fetch()['v'] ?? 'unknown';
    $who = $pdo->query("select current_user u")->fetch()['u'] ?? 'unknown';
    $db  = $pdo->query("select current_database() d")->fetch()['d'] ?? 'unknown';
    echo "OK\nuser=$who\ndb=$db\nversion=$ver\n";
} catch (Throwable $e) {
    http_response_code(500);
    echo "QUERY_ERROR: " . $e->getMessage() . PHP_EOL;
}

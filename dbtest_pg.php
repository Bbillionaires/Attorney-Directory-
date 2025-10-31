<?php
header('Content-Type: text/plain');
require __DIR__ . '/conn_pg.php';
echo "DB CONNECT (Postgres): OK\n";
$r = $pdo->query("SELECT 1 AS ok")->fetch();
echo "TEST QUERY: " . json_encode($r) . "\n";

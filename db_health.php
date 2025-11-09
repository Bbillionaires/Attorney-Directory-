<?php
require __DIR__ . '/db.php';

header('Content-Type: text/plain');

echo "DB HEALTH CHECK\n";
echo "===============\n";

try {
    $pdo = getPDO();
    echo "OK: got PDO\n";

    $row = $pdo->query("SELECT NOW() AS now")->fetch();
    echo "NOW(): " . $row['now'] . "\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

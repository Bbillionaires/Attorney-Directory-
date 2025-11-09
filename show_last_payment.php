<?php
require __DIR__ . '/db.php';

try {
    $pdo = getPDO();
    $rows = $pdo->query("
        SELECT *
        FROM payments
        ORDER BY created_at DESC
        LIMIT 1
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($rows, JSON_PRETTY_PRINT) . PHP_EOL;
} catch (Throwable $e) {
    echo "DB ERROR: " . $e->getMessage() . PHP_EOL;
}

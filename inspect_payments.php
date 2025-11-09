<?php
require __DIR__ . '/db.php';

try {
    $pdo = getPDO();

    echo "COLUMNS:\n";
    $cols = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name='payments' ORDER BY ordinal_position")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($cols as $c) {
        echo " - {$c}\n";
    }

    echo "\nLAST 10 ROWS:\n";
    $rows = $pdo->query("SELECT * FROM payments ORDER BY created_at DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows, JSON_PRETTY_PRINT) . "\n";

} catch (Throwable $e) {
    echo "DB ERROR: " . $e->getMessage() . "\n";
}

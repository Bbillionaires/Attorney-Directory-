<?php
ini_set('display_errors',1); error_reporting(E_ALL);
require __DIR__.'/conn.php';
$sql = "SELECT ItemID, Title, CategoryID, active FROM dd_catalog WHERE active=1 ORDER BY ItemID LIMIT 20";
$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll();
header('Content-Type: text/plain');
echo "Rows: ".count($rows).PHP_EOL;
foreach ($rows as $r) {
  echo "{$r['ItemID']} | {$r['Title']} | Cat: {$r['CategoryID']} | active={$r['active']}\n";
}

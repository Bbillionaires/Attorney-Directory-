<?php
ini_set('display_errors',1); error_reporting(E_ALL);
require __DIR__.'/conn.php';
$sql = "SELECT c.ItemID, c.Title, cat.CatName
        FROM dd_catalog c
        LEFT JOIN dd_categories cat ON cat.CatID = c.CategoryID
        WHERE c.active=1
        ORDER BY c.ItemID
        LIMIT 20";
foreach ($pdo->query($sql) as $r) {
  echo "{$r['ItemID']} | {$r['Title']} | {$r['CatName']}\n";
}

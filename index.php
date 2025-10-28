<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
require_once __DIR__ . '/includes.php';

$item = db_one("
  SELECT c.itemid, c.itemname, c.itemdesc, COALESCE(c.itemprice,0) AS itemprice
  FROM dd_catalog c
  WHERE COALESCE(c.active,1)=1
  ORDER BY RANDOM()
  LIMIT 1
");

include_once __DIR__ . '/templates/HeaderTemplate.php';
?>
<main>
  <h2>Attorney Directory Listing</h2>
  <?php if ($item): ?>
    <p><strong><?=htmlspecialchars($item['itemname'])?></strong> — $<?=number_format((float)$item['itemprice'],2)?></p>
    <p><?=nl2br(htmlspecialchars($item['itemdesc'] ?? ''))?></p>
  <?php else: ?>
    <p>No items yet. Visit <a href="/list.php">/list.php</a> to verify listing.</p>
  <?php endif; ?>
</main>
<?php include_once __DIR__ . '/templates/FooterTemplate.php'; ?>

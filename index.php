<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/includes.php'; // must set $pdo (PDO)

$item = null;
try {
  $sql = "
    SELECT itemid, itemname, itemdesc, COALESCE(itemprice,0) AS itemprice
    FROM dd_catalog
    WHERE COALESCE(active,1) = 1
    ORDER BY RANDOM()
    LIMIT 1
  ";
  $stmt = $pdo->query($sql);
  $item = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
} catch (Throwable $e) {
  echo 'DB error: ' . htmlspecialchars($e->getMessage());
}

@include __DIR__ . '/templates/HeaderTemplate.php';
?>
<main>
  <h2>Attorney Directory</h2>
  <?php if ($item): ?>
    <p><strong><?=htmlspecialchars($item['itemname'])?></strong>
       — $<?=number_format((float)($item['itemprice'] ?? 0), 2)?></p>
    <p><?=nl2br(htmlspecialchars($item['itemdesc'] ?? ''))?></p>
  <?php else: ?>
    <p>No items found. Visit <a href="/list.php">/list.php</a> to view all entries.</p>
  <?php endif; ?>
</main>
<?php @include __DIR__ . '/templates/FooterTemplate.php'; ?>

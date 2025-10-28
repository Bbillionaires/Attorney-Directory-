<?php
declare(strict_types=1);
require __DIR__.'/conn.php';
$stmt = $pdo->query("SELECT itemid,itemname,itemprice,itemthumb FROM dd_catalog WHERE COALESCE(active,1)=1 ORDER BY itemid DESC");
$rows = $stmt->fetchAll();
?>
<?php include __DIR__.'/templates/HeaderTemplate.php'; ?>
<h2>Public Directory</h2>
<?php if (!$rows): ?>
  <p class="muted">No public items yet.</p>
<?php else: ?>
  <ul>
    <?php foreach ($rows as $r): ?>
      <li>
        <a href="/view_item.php?id=<?= (int)$r['itemid'] ?>"><?= htmlspecialchars($r['itemname']) ?></a>
        — $<?= number_format((float)$r['itemprice'],2) ?>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
<?php include __DIR__.'/templates/FooterTemplate.php'; ?>

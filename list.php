<?php
require __DIR__ . '/conn.php';
$showAll = (($_GET['show'] ?? '') === 'all');
$sql = $showAll
  ? 'SELECT itemid,itemname,itemprice,active FROM dd_catalog ORDER BY itemid DESC'
  : 'SELECT itemid,itemname,itemprice,active FROM dd_catalog WHERE COALESCE(active, true)=true ORDER BY itemid DESC';
$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<meta charset="utf-8">
<title>Attorney Directory</title>
<h1>Attorney Directory</h1>
<p>
  <a class="btn" href="/add.php">+ New item</a>
  <a class="btn" href="/list.php">Active only</a>
  <a class="btn" href="/list.php?show=all">Show all</a>
</p>
<?php if (!$rows): ?>
  <p>No items found. <a href="/add.php">Add one</a>.</p>
<?php else: ?>
  <ul>
  <?php foreach ($rows as $r): ?>
    <li>
      <a href="/view_item.php?id=<?= (int)$r['itemid'] ?>">
        <?= htmlspecialchars($r['itemname']) ?>
      </a>
      — $<?= number_format((float)$r['itemprice'],2) ?>
      <?= !empty($r['active']) ? '' : '(inactive)' ?>
    </li>
  <?php endforeach; ?>
  </ul>
<?php endif; ?>

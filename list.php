<?php
$showAll = (($_GET['show'] ?? '') === 'all');
$sql = $showAll
  ? 'SELECT itemid,itemname,itemprice,active FROM dd_catalog ORDER BY itemid DESC'
  : 'SELECT itemid,itemname,itemprice,active FROM dd_catalog WHERE COALESCE(active,1)=1 ORDER BY itemid DESC';
$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<meta charset="utf-8">
<title>Attorney Directory</title>
<h1 class="text-2xl font-semibold mb-4">Attorney Directory</h1>
<p>
  <a class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 text-white px-4 py-2 hover:bg-emerald-700 active:bg-emerald-800" href="/add.php">+ New item</a>
  <a class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 text-white px-4 py-2 hover:bg-emerald-700 active:bg-emerald-800" href="/list.php">Active only</a>
  <a class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 text-white px-4 py-2 hover:bg-emerald-700 active:bg-emerald-800" href="/list.php?show=all">Show all</a>
</p>
<?php if (!$rows): ?>
  <p>No items found. <a class="text-emerald-700 hover:underline" href="/add.php">Add one</a>.</p>
<?php else: ?>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
  <?php foreach ($rows as $r): ?>
    <li class="py-2">
      <a class="text-emerald-700 hover:underline" href="/view_item.php?id=<?= (int)$r['itemid'] ?>">
        <?= htmlspecialchars($r['itemname']) ?>
      </a>
      — $<?= number_format((float)$r['itemprice'],2) ?>
      <?= !empty($r['active']) ? '' : '(inactive)' ?>
    </div>
  <?php endforeach; ?>
  </div>
<?php endif; ?>

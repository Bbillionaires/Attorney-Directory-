<?php
error_reporting(E_ALL); ini_set('display_errors', '1');
require_once __DIR__ . '/includes.php';
@include __DIR__ . '/templates/HeaderTemplate.php';

$pdo = pdo();
$featured = [];
$dbMsg = '';

if ($pdo instanceof PDO) {
  try {
    $stmt = $pdo->query("
      SELECT itemid, itemname, itemdesc, itemprice, itemthumb
      FROM dd_catalog
      WHERE COALESCE(active,1)=1
      ORDER BY itemid DESC
      LIMIT 12
    ");
    $featured = $stmt->fetchAll();
  } catch (Throwable $e) {
    $dbMsg = 'DB error: ' . $e->getMessage();
  }
} else {
  $dbMsg = 'DB not connected' . (!empty($GLOBALS['DB_ERROR']) ? (': '.$GLOBALS['DB_ERROR']) : '');
}
?>
<div class="container mx-auto px-4 py-8">
  <div class="rounded-2xl bg-slate-800/40 p-8 mb-8">
    <h1 class="text-3xl font-semibold text-white">Professional Legal Templates</h1>
    <p class="text-slate-300 mt-2">Browse, compare, and purchase agreements. Clean UI, simple checkout.</p>
  </div>

  <?php if ($dbMsg): ?>
    <div style="color:#fda4af; margin-bottom:1rem;"><?= htmlspecialchars($dbMsg) ?></div>
  <?php endif; ?>

  <?php if (!$featured): ?>
    <div>No items yet. <a href="/public_list.php">View all</a></div>
  <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach ($featured as $row): ?>
        <a class="block rounded-xl p-4 border border-slate-700 hover:bg-slate-800/40"
           href="/view_item.php?id=<?= (int)$row['itemid'] ?>">
          <div class="text-lg font-medium text-white"><?= htmlspecialchars($row['itemname']) ?></div>
          <div class="text-slate-300 text-sm mt-1"><?= htmlspecialchars($row['itemdesc']) ?></div>
          <div class="text-sky-300 font-semibold mt-2">$<?= number_format((float)$row['itemprice'], 2) ?></div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<?php @include __DIR__ . '/templates/FooterTemplate.php'; ?>

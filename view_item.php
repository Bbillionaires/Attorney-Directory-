<?php
declare(strict_types=1);

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM dd_catalog WHERE itemid=:id");
$stmt->execute([':id'=>$id]);
$row = $stmt->fetch();

include __DIR__.'/templates/HeaderTemplate.php';
if (!$row) {
  echo '<p class="muted">Item not found.</p>';
  include __DIR__.'/templates/FooterTemplate.php'; exit;
}
?>
<h2><?= htmlspecialchars($row['itemname']) ?></h2>
<p><?= nl2br(htmlspecialchars($row['itemdesc'] ?? '')) ?></p>
<p><strong>Price:</strong> $<?= number_format((float)$row['itemprice'],2) ?></p>
<?php if (!empty($row['itemthumb'])): ?>
  <p><img class="max-w-full rounded-xl shadow mb-4" src="<?= htmlspecialchars($row['itemthumb']) ?>" style="max-width:320px;border:1px solid #ddd;border-radius:6px"></p>
<?php endif; ?>
<p>
  <a class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 text-white px-4 py-2 hover:bg-emerald-700 active:bg-emerald-800" href="/add.php?id=<?= (int)$row['itemid'] ?>">Edit</a>
  <a class="inline-flex items-center gap-2 rounded-lg bg-white text-emerald-700 border border-emerald-600 px-4 py-2 hover:bg-emerald-50" href="/list.php">Admin List</a>
  <a class="inline-flex items-center gap-2 rounded-lg bg-white text-emerald-700 border border-emerald-600 px-4 py-2 hover:bg-emerald-50" href="/public_list.php">Public Directory</a>
</p>

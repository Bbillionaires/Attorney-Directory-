<?php include __DIR__ . '/templates/HeaderTemplate.php'; ?>
<?php require __DIR__ . '/conn.php'; require __DIR__ . '/lib/db.php'; ?>

<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$row = db_one(
  "SELECT itemid, itemname, itemdesc, itemprice, itemthumb
   FROM dd_catalog WHERE itemid = :id",
  [':id' => $id]
);
?>

<section class="card">
  <?php if (!$row): ?>
    <p class="muted">Item not found.</p>
    <p><a class="btn secondary" href="/list.php">Back</a></p>
  <?php else: ?>
    <h2><?= htmlspecialchars($row['itemname'] ?? '') ?></h2>
    <?php if (!empty($row['itemthumb'])): ?>
      <p><img src="<?= htmlspecialchars($row['itemthumb']) ?>" style="max-width:100%;height:auto" alt=""></p>
    <?php endif; ?>
    <p><?= nl2br(htmlspecialchars($row['itemdesc'] ?? '')) ?></p>
    <p><strong>$<?= number_format((float)($row['itemprice'] ?? 0), 2) ?></strong></p>
    <p><a class="btn secondary" href="/list.php">Back to list</a></p>
  <?php endif; ?>
</section>

<?php include __DIR__ . '/templates/FooterTemplate.php'; ?>

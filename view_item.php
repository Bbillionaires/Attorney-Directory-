<?php
require __DIR__ . '/includes.php';
$pdo = pdo();
$id  = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$row = null;
if ($id > 0) {
  $stmt = $pdo->prepare('SELECT * FROM dd_catalog WHERE itemid = :id');
  $stmt->execute([':id' => $id]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<?php include __DIR__ . '/templates/HeaderTemplate.php'; ?>

<?php if (!$row): ?>
  <p class="muted">Item not found. <a href="/list.php">Back to list</a></p>
<?php else: ?>
  <section class="card">
    <h2><?= htmlspecialchars($row['itemname']) ?></h2>
    <p class="price">$<?= number_format((float)$row['itemprice'], 2) ?></p>
    <p><?= nl2br(htmlspecialchars($row['itemdesc'] ?? '')) ?></p>
    <?php if (!empty($row['itemthumb'])): ?>
      <p><img src="<?= htmlspecialchars($row['itemthumb']) ?>" style="max-width:360px"></p>
    <?php endif; ?>
    <p>
      <a class="btn" href="/add.php?id=<?= (int)$row['itemid'] ?>">Edit</a>
      <a class="btn secondary" href="/delete.php?id=<?= (int)$row['itemid'] ?>" onclick="return confirm('Delete this item?')">Delete</a>
      <a class="btn secondary" href="/list.php">Back</a>
    </p>
  </section>
<?php endif; ?>

<?php include __DIR__ . '/templates/FooterTemplate.php'; ?>

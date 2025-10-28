<?php
require __DIR__ . '/includes.php';
$pdo = pdo();
$rows = $pdo->query("SELECT itemid, itemname, itemprice FROM dd_catalog ORDER BY itemid DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include __DIR__ . '/templates/HeaderTemplate.php'; ?>
<h2>Attorney Directory Listing</h2>

<p><a class="btn" href="/add.php">+ Add new item</a></p>

<?php if (!$rows): ?>
  <p class="muted">No items found.</p>
<?php else: ?>
  <ul>
    <?php foreach ($rows as $r): ?>
      <li>
        #<?= (int)$r['itemid'] ?> — <?= htmlspecialchars($r['itemname']) ?>
        — $<?= number_format((float)$r['itemprice'], 2) ?>
        &nbsp;|&nbsp;
        <a href="/view_item.php?id=<?= (int)$r['itemid'] ?>">View</a>
        &nbsp;|&nbsp;
        <a href="/add.php?id=<?= (int)$r['itemid'] ?>">Edit</a>
        &nbsp;|&nbsp;
        <a href="/delete.php?id=<?= (int)$r['itemid'] ?>" onclick="return confirm('Delete this item?')">Delete</a>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<?php include __DIR__ . '/templates/FooterTemplate.php'; ?>

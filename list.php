<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/includes.php'; // must set $pdo

$rows = [];
try {
  $sql = "
    SELECT itemid, itemname, COALESCE(itemprice,0) AS itemprice
    FROM dd_catalog
    WHERE COALESCE(active,1) = 1
    ORDER BY itemid DESC
    LIMIT 50
  ";
  $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
  echo 'DB error: ' . htmlspecialchars($e->getMessage());
}

@include __DIR__ . '/templates/HeaderTemplate.php';
?>
<main>
  <h2>Attorney Directory Listing</h2>
  <?php if (!$rows): ?>
    <p>No items yet.</p>
  <?php else: ?>
    <ul>
      <?php foreach ($rows as $r): ?>
        <li>#<?= (int)$r['itemid'] ?> —
            <?= htmlspecialchars($r['itemname']) ?> —
            $<?= number_format((float)$r['itemprice'], 2) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</main>
<?php @include __DIR__ . '/templates/FooterTemplate.php'; ?>

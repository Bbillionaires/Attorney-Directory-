<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
echo "MARKER: INDEX TOP<br>";
require_once __DIR__ . '/includes.php'; // must define $pdo (PDO)

try {
  $sql = "SELECT itemid, itemname, COALESCE(itemprice,0) AS itemprice
          FROM dd_catalog
          WHERE COALESCE(active,1)=1
          ORDER BY itemid DESC
          LIMIT 50";
  $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
  die("DB ERR: ".htmlspecialchars($e->getMessage()));
}

@include __DIR__ . '/templates/HeaderTemplate.php';
echo "<p>MARKER: AFTER HEADER</p>";
?>
<main>
  <h2>Attorney Directory Listing</h2>
  <?php if (!$rows): ?>
    <p>MARKER: NO ROWS</p>
  <?php else: ?>
    <ul>
      <?php foreach ($rows as $r): ?>
        <li>#<?= (int)$r['itemid'] ?> — <?= htmlspecialchars($r['itemname']) ?> — $<?= number_format((float)$r['itemprice'],2) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</main>
<?php
echo "<p>MARKER: BEFORE FOOTER</p>";
@include __DIR__ . '/templates/FooterTemplate.php';
echo "<p>MARKER: INDEX BOTTOM</p>";

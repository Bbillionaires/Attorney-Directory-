<?php include __DIR__ . '/templates/HeaderTemplate.php'; ?>
<?php require __DIR__ . '/conn.php'; require __DIR__ . '/lib/db.php'; ?>

<section class="card">
  <h2>All Items</h2>
  <?php
    $rows = db_all(
      "SELECT itemid, itemname, itemprice FROM dd_catalog
       WHERE active IS NULL OR active=1
       ORDER BY itemid DESC"
    );
    if (!$rows) {
      echo '<p class="muted">No items yet.</p>';
    } else {
      echo '<ul>';
      foreach ($rows as $r) {
        $name = htmlspecialchars($r['itemname'] ?? '');
        $price = number_format((float)($r['itemprice'] ?? 0), 2);
        $id = (int)$r['itemid'];
        echo "<li><a href=\"/view_item.php?id=$id\">$name</a> — \$$price</li>";
      }
      echo '</ul>';
    }
  ?>
  <p><a class="btn" href="/add.php">Add new</a></p>
</section>

<?php include __DIR__ . '/templates/FooterTemplate.php'; ?>

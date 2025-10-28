<?php include __DIR__ . '/templates/HeaderTemplate.php'; ?>
<?php require __DIR__ . '/conn.php'; require __DIR__ . '/lib/db.php'; ?>

<?php
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name  = trim($_POST['itemname'] ?? '');
  $desc  = trim($_POST['itemdesc'] ?? '');
  $price = (float)($_POST['itemprice'] ?? 0);
  $thumb = trim($_POST['itemthumb'] ?? '');

  if ($name === '') {
    $err = 'Name is required.';
  } else {
    $sql = "INSERT INTO dd_catalog (itemname, itemdesc, itemprice, itemthumb, active)
            VALUES (:n, :d, :p, :t, 1)
            RETURNING itemid";
    $row = db_one($sql, [
      ':n' => $name,
      ':d' => $desc,
      ':p' => $price,
      ':t' => $thumb,
    ]);
    if ($row && isset($row['itemid'])) {
      header("Location: /view_item.php?id=".(int)$row['itemid']);
      exit;
    }
    $err = 'Insert failed.';
  }
}
?>

<section class="card">
  <h2>Add Item</h2>
  <?php if ($err): ?><p style="color:#b00020"><?= htmlspecialchars($err) ?></p><?php endif; ?>
  <form method="post" action="/add.php">
    <p>
      <label>Name<br>
        <input name="itemname" required style="width:100%" maxlength="255">
      </label>
    </p>
    <p>
      <label>Description<br>
        <textarea name="itemdesc" rows="5" style="width:100%"></textarea>
      </label>
    </p>
    <p>
      <label>Price (e.g. 9.99)<br>
        <input name="itemprice" type="number" step="0.01" min="0" value="0.00">
      </label>
    </p>
    <p>
      <label>Image URL (optional)<br>
        <input name="itemthumb" type="url" placeholder="https://…">
      </label>
    </p>
    <p>
      <button class="btn" type="submit">Save</button>
      <a class="btn secondary" href="/list.php" style="margin-left:8px">Cancel</a>
    </p>
  </form>
</section>

<?php include __DIR__ . '/templates/FooterTemplate.php'; ?>

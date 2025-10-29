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
  <div class="max-w-3xl mx-auto px-4 py-8"><div class="bg-white rounded-2xl shadow p-6"><form method="post" action="/save_item.php">
    <p>
      <label class="block font-medium text-sm mb-1">Name<br>
        <input class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500" name="itemname" required style="width:100%" maxlength="255">
      </label>
    </p>
    <p>
      <label class="block font-medium text-sm mb-1">Description<br>
        <textarea class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500" name="itemdesc" rows="5" style="width:100%"></textarea>
      </label>
    </p>
    <p>
      <label class="block font-medium text-sm mb-1">Price (e.g. 9.99)<br>
        <input class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500" name="itemprice" type="number" step="0.01" min="0" value="0.00">
      </label>
    </p>
    <p>
      <label class="block font-medium text-sm mb-1">Image URL (optional)<br>
        <input class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500" name="itemthumb" type="url" placeholder="https://…">
      </label>
    </p>
    <p>
      <button class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 text-white px-4 py-2 hover:bg-emerald-700 active:bg-emerald-800" type="submit">Save</button>
      <a class="inline-flex items-center gap-2 rounded-lg bg-white text-emerald-700 border border-emerald-600 px-4 py-2 hover:bg-emerald-50" href="/list.php" style="margin-left:8px">Cancel</a>
    </p>
  </form></div></div>
    <p><label class="block font-medium text-sm mb-1"><input class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500" type="checkbox" name="active" value="1" <?php echo empty($row["active"]) ? "" : "checked"; ?>> Active</label></p>
</section>

<?php include __DIR__ . '/templates/FooterTemplate.php'; ?>

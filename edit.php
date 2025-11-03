<?php
declare(strict_types=1);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare('SELECT itemid,itemname,itemdesc,itemprice,itemthumb,active FROM dd_catalog WHERE itemid=:id');
$stmt->execute([':id'=>$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$row){ http_response_code(404); exit('Not found'); }
?>
<!doctype html>
<meta charset="utf-8">
<title>Edit <?= htmlspecialchars($row['itemname']) ?> – Attorney Directory</title>
<h1>Edit: <?= htmlspecialchars($row['itemname']) ?></h1>

<form method="post" action="/save_item.php" class="stack">
  <input type="hidden" name="id" value="<?= (int)$row['itemid'] ?>">
  <p><label>Name<br>
    <input name="itemname" value="<?= htmlspecialchars($row['itemname']) ?>" required></label></p>
  <p><label>Description<br>
    <textarea name="itemdesc" rows="6"><?= htmlspecialchars($row['itemdesc'] ?? '') ?></textarea></label></p>
  <p><label>Price ($)<br>
    <input name="itemprice" type="number" step="0.01" value="<?= htmlspecialchars((string)$row['itemprice']) ?>"></label></p>
  <p><label>Image URL<br>
    <input name="itemthumb" type="url" value="<?= htmlspecialchars($row['itemthumb'] ?? '') ?>"></label></p>
  <p><label><input type="checkbox" name="active" <?= !empty($row['active'])?'checked':''; ?>> Active</label></p>
  <p>
    <button class="btn" type="submit">Save</button>
    <a class="btn" href="/view_item.php?id=<?= (int)$row['itemid'] ?>">Cancel</a>
  </p>
</form>
<p><a href="/list.php">← Back to list</a></p>

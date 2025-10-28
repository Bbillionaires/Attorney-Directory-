<?php
declare(strict_types=1);
require __DIR__ . '/conn.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare('SELECT itemid,itemname,itemdesc,itemprice,itemthumb,active FROM dd_catalog WHERE itemid=:id');
$stmt->execute([':id'=>$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$row){ http_response_code(404); exit('Not found'); }
?>
<!doctype html>
<meta charset="utf-8">
<title><?= htmlspecialchars($row['itemname']) ?> – Attorney Directory</title>
<h1><?= htmlspecialchars($row['itemname']) ?></h1>
<p><?= nl2br(htmlspecialchars($row['itemdesc'] ?? '')) ?></p>
<p><strong>Price:</strong> $<?= number_format((float)$row['itemprice'],2) ?></p>
<?php if(!empty($row['itemthumb'])): ?>
  <p><img src="<?= htmlspecialchars($row['itemthumb']) ?>" alt="" style="max-width:320px"></p>
<?php endif; ?>
<p><strong>Status:</strong> <?= !empty($row['active']) ? 'Active' : 'Inactive' ?></p>
<p>
  <a class="btn" href="/edit.php?id=<?= (int)$row['itemid'] ?>">Edit</a>
  <a class="btn" href="/toggle_active.php?id=<?= (int)$row['itemid'] ?>">Toggle Active</a>
  <a class="btn" href="/delete_item.php?id=<?= (int)$row['itemid'] ?>" onclick="return confirm('Delete this item?')">Delete</a>
  <a class="btn" href="/list.php">Back to list</a>
</p>

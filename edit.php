<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
require_once __DIR__ . '/includes.php';
@include __DIR__ . '/templates/HeaderTemplate.php';

$need  = getenv('ADMIN_TOKEN') ?: '';
$token = $_GET['token'] ?? '';
if ($need === '' || $token !== $need) {
  http_response_code(403);
  echo '<p class="muted">Forbidden. Add <code>?token=YOURTOKEN</code> to the URL.</p>';
  @include __DIR__ . '/templates/FooterTemplate.php'; exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { echo '<p class="muted">Invalid id.</p>'; @include __DIR__ . '/templates/FooterTemplate.php'; exit; }

$st = $pdo->prepare("SELECT itemid,itemname,itemdesc,itemprice,itemthumb,active
                     FROM dd_catalog WHERE itemid=:id");
$st->execute([':id'=>$id]);
$row = $st->fetch(PDO::FETCH_ASSOC);
if (!$row) { echo '<p class="muted">Item not found.</p>'; @include __DIR__ . '/templates/FooterTemplate.php'; exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name  = trim($_POST['itemname'] ?? '');
  $desc  = trim($_POST['itemdesc'] ?? '');
  $price = trim($_POST['itemprice'] ?? '0');
  $thumb = trim($_POST['itemthumb'] ?? '');
  $active = isset($_POST['active']) ? 1 : 0;

  if ($name === '') { $errors[] = 'Name is required.'; }
  if ($price === '' || !is_numeric($price) || $price < 0) { $errors[] = 'Price must be a non-negative number.'; }

  if (!$errors) {
    try {
      $u = $pdo->prepare("UPDATE dd_catalog
                          SET itemname=:n,itemdesc=:d,itemprice=:p,itemthumb=:t,active=:a
                          WHERE itemid=:id");
      $u->execute([
        ':n'=>$name, ':d'=>$desc, ':p'=>(float)$price, ':t'=>$thumb, ':a'=>(int)$active, ':id'=>$id
      ]);
      header('Location: /view_item.php?id='.$id.'&token='.$token);
      exit;
    } catch (Throwable $e) {
      $errors[] = 'DB error: '.$e->getMessage();
    }
  }
} else {
  // seed form from db
  $_POST = $row;
}
?>
<h2>Edit Item #<?= $id ?></h2>

<?php if ($errors): ?>
  <div class="card" style="border-left:4px solid #dc2626;background:#fff1f2">
    <p><strong>There were problems:</strong></p>
    <ul><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
  </div>
<?php endif; ?>

<form method="post" action="/edit.php?id=<?= $id ?>&token=<?= htmlspecialchars($token) ?>" class="card">
  <label>Name *</label>
  <input type="text" name="itemname" required value="<?= htmlspecialchars($_POST['itemname'] ?? '') ?>">

  <label>Description</label>
  <textarea name="itemdesc" rows="6"><?= htmlspecialchars($_POST['itemdesc'] ?? '') ?></textarea>

  <div class="row">
    <div>
      <label>Price (USD) *</label>
      <input type="number" min="0" step="0.01" name="itemprice" value="<?= htmlspecialchars($_POST['itemprice'] ?? '0.00') ?>" required>
    </div>
    <div>
      <label>Thumbnail URL</label>
      <input type="url" name="itemthumb" value="<?= htmlspecialchars($_POST['itemthumb'] ?? '') ?>">
    </div>
  </div>

  <label class="muted">
    <input type="checkbox" name="active" <?= (int)($_POST['active'] ?? 0)===1?'checked':''; ?>> Active
  </label>

  <p>
    <button class="btn" type="submit">Save Changes</button>
    <a class="btn secondary" href="/view_item.php?id=<?= $id ?>&token=<?= htmlspecialchars($token) ?>">Cancel</a>
  </p>
</form>

<?php @include __DIR__ . '/templates/FooterTemplate.php';

<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
require_once __DIR__ . '/includes.php';
@include __DIR__ . '/templates/HeaderTemplate.php';

/** ---- tiny auth ----
 * You must pass ?token=... that matches the ADMIN_TOKEN environment variable.
 * Example: /add.php?token=YOURTOKEN
 */
$need = getenv('ADMIN_TOKEN') ?: '';
$token = $_GET['token'] ?? '';
if ($need === '' || $token !== $need) {
  http_response_code(403);
  echo '<p class="muted">Forbidden. Add <code>?token=YOURTOKEN</code> to the URL.</p>';
  @include __DIR__ . '/templates/FooterTemplate.php';
  exit;
}

$errors = [];
$okId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name  = trim($_POST['itemname'] ?? '');
  $desc  = trim($_POST['itemdesc'] ?? '');
  $price = trim($_POST['itemprice'] ?? '0');
  $thumb = trim($_POST['itemthumb'] ?? '');
  $active = isset($_POST['active']) ? 1 : 0;

  if ($name === '')   { $errors[] = 'Name is required.'; }
  if ($price === '' || !is_numeric($price) || $price < 0) { $errors[] = 'Price must be a non-negative number.'; }

  if (!$errors) {
    try {
      $stmt = $pdo->prepare("INSERT INTO dd_catalog (itemname, itemdesc, itemprice, itemthumb, active)
                             VALUES (:n,:d,:p,:t,:a)
                             RETURNING itemid");
      $stmt->execute([
        ':n' => $name,
        ':d' => $desc,
        ':p' => (float)$price,
        ':t' => $thumb,
        ':a' => (int)$active,
      ]);
      $okId = (int)$stmt->fetchColumn();
      header('Location: /view_item.php?id=' . $okId);
      exit;
    } catch (Throwable $e) {
      $errors[] = 'DB error: ' . $e->getMessage();
    }
  }
}
?>
<h2>Add Directory Item</h2>

<?php if ($errors): ?>
  <div class="card" style="border-left:4px solid #dc2626;background:#fff1f2">
    <p><strong>There were problems:</strong></p>
    <ul>
      <?php foreach ($errors as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post" action="/add.php?token=<?= htmlspecialchars($token) ?>" class="card">
  <label>Name *</label>
  <input type="text" name="itemname" required placeholder="e.g. Service Agreement">

  <label>Description</label>
  <textarea name="itemdesc" rows="6" placeholder="Short description"></textarea>

  <div class="row">
    <div>
      <label>Price (USD) *</label>
      <input type="number" name="itemprice" min="0" step="0.01" value="0.00" required>
    </div>
    <div>
      <label>Thumbnail URL (optional)</label>
      <input type="url" name="itemthumb" placeholder="https://.../image.jpg">
    </div>
  </div>

  <label class="muted">
    <input type="checkbox" name="active" checked> Active (visible on site)
  </label>

  <p>
    <button class="btn" type="submit">Save Item</button>
    <a class="btn secondary" href="/list.php">Cancel</a>
  </p>
</form>

<?php @include __DIR__ . '/templates/FooterTemplate.php';

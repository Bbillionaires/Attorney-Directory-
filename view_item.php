<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
require_once __DIR__ . '/includes.php';
@include __DIR__ . '/templates/HeaderTemplate.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$token = $_GET['token'] ?? '';
$need  = getenv('ADMIN_TOKEN') ?: '';

$row = null;
if ($id > 0) {
  $st = $pdo->prepare("SELECT itemid,itemname,itemdesc,itemprice,itemthumb,active
                       FROM dd_catalog WHERE itemid = :id");
  $st->execute([':id' => $id]);
  $row = $st->fetch(PDO::FETCH_ASSOC);
}

if (!$row) {
  echo "<p class='muted'>Item not found.</p>";
  @include __DIR__ . '/templates/FooterTemplate.php'; exit;
}
?>
<h2><?= htmlspecialchars($row['itemname']) ?></h2>

<?php if (!empty($row['itemthumb'])): ?>
  <p><img alt="thumb" src="<?= htmlspecialchars($row['itemthumb']) ?>" style="max-width:260px;height:auto;border-radius:12px"></p>
<?php endif; ?>

<p><strong>Price:</strong> $<?= number_format((float)$row['itemprice'], 2) ?></p>
<p><?= nl2br(htmlspecialchars((string)$row['itemdesc'])) ?></p>
<p><span class="muted">Status:</span> <?= ((int)$row['active']===1 ? 'Active' : 'Hidden') ?></p>

<p>
  <a class="btn" href="/list.php">← Back to list</a>
  <?php if ($need !== '' && $token === $need): ?>
    <a class="btn" href="/edit.php?id=<?= $row['itemid'] ?>&token=<?= htmlspecialchars($token) ?>">Edit</a>
    <a class="btn danger" href="/delete.php?id=<?= $row['itemid'] ?>&token=<?= htmlspecialchars($token) ?>">Delete</a>
  <?php endif; ?>
</p>

<?php @include __DIR__ . '/templates/FooterTemplate.php';

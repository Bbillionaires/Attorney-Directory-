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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $d = $pdo->prepare("DELETE FROM dd_catalog WHERE itemid=:id");
    $d->execute([':id'=>$id]);
    header('Location: /list.php?token='.$token);
    exit;
  } catch (Throwable $e) {
    echo '<p class="muted">Delete failed: '.htmlspecialchars($e->getMessage()).'</p>';
  }
} else {
  $st = $pdo->prepare("SELECT itemname FROM dd_catalog WHERE itemid=:id");
  $st->execute([':id'=>$id]);
  $name = $st->fetchColumn();
  if (!$name) { echo '<p class="muted">Item not found.</p>'; @include __DIR__ . '/templates/FooterTemplate.php'; exit; }
}
?>
<h2>Delete Item #<?= $id ?></h2>
<p>Are you sure you want to delete <strong><?= htmlspecialchars($name ?? '') ?></strong>?</p>
<form method="post" action="/delete.php?id=<?= $id ?>&token=<?= htmlspecialchars($token) ?>">
  <button class="btn danger" type="submit">Yes, delete</button>
  <a class="btn secondary" href="/view_item.php?id=<?= $id ?>&token=<?= htmlspecialchars($token) ?>">Cancel</a>
</form>
<?php @include __DIR__ . '/templates/FooterTemplate.php';

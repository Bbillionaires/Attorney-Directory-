<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
require_once __DIR__ . '/includes.php';
@include __DIR__ . '/templates/HeaderTemplate.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT itemid,itemname,itemdesc,itemprice,itemthumb
                       FROM dd_catalog
                       WHERE itemid=:id AND COALESCE(active,1)=1");
$stmt->execute([':id'=>$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$item) { echo "<p class='muted'>Item not found.</p>"; @include __DIR__ . '/templates/FooterTemplate.php'; exit; }
?>
<article class="card">
  <div class="row">
    <div>
      <?php if (!empty($item['itemthumb'])): ?>
        <img src="<?= htmlspecialchars($item['itemthumb']) ?>" style="max-width:100%;border-radius:6px">
      <?php endif; ?>
    </div>
    <div>
      <h2><?= htmlspecialchars($item['itemname']) ?></h2>
      <p class="muted"><?= nl2br(htmlspecialchars($item['itemdesc'] ?? '')) ?></p>
      <p class="price"><span class="badge">$<?= number_format((float)$item['itemprice'],2) ?></span></p>
      <p><a class="btn secondary" href="/list.php">← Back to list</a></p>
    </div>
  </div>
</article>
<?php @include __DIR__ . '/templates/FooterTemplate.php';

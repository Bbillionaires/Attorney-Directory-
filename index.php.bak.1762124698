<?php require __DIR__ . "/conn.php"; require __DIR__ . "/lib/db.php"; ?>
require_once __DIR__ '/conn.php';
require_once __DIR__ '/conn.php';
<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
require_once __DIR__ . '/includes.php';
@include __DIR__ . '/templates/HeaderTemplate.php';

$featured = [];
try {
  $stmt = $pdo->query("SELECT itemid,itemname,itemdesc,itemprice,itemthumb
                       FROM dd_catalog
                       WHERE COALESCE(active,1)=1
                       ORDER BY itemid DESC
                       LIMIT 1");
  $featured = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
} catch (Throwable $e) {
  echo '<p class="muted">DB error: '.htmlspecialchars($e->getMessage()).'</p>';
}
?>
<?php if ($featured): ?>
<section class="card">
  <div class="row">
    <div>
      <?php if (!empty($featured['itemthumb'])): ?>
        <img src="<?= htmlspecialchars($featured['itemthumb']) ?>" style="max-width:100%;border-radius:6px">
      <?php endif; ?>
    </div>
    <div>
      <h2><?= htmlspecialchars($featured['itemname']) ?></h2>
      <p class="muted"><?= nl2br(htmlspecialchars($featured['itemdesc'] ?? '')) ?></p>
      <p class="price"><span class="badge">$<?= number_format((float)$featured['itemprice'],2) ?></span></p>
      <p>
        <a class="btn" href="/view_item.php?id=<?= (int)$featured['itemid'] ?>">View</a>
        <a class="btn secondary" href="/list.php">View all</a>
      </p>
    </div>
  </div>
</section>
<?php else: ?>
  <p class="muted">No items yet. <a href="/list.php">View all</a></p>
<?php endif; ?>

<p class="mt-4"><a class="btn" href="/add.php">&#x2795; Add new item</a></p>
<?php include __DIR__ . '/templates/FooterTemplate.php'; ?>
  <p class="mt-4"><a class="btn secondary" href="/public_list.php">Public Directory View</a></p>
<?php include __DIR__ . '/templates/FooterTemplate.php'; ?>
</body>
</html>

<?php
// --- safety fallback for homepage queries ---
if (!isset($pdo) || !($pdo instanceof PDO)) {
    $db_msg = $GLOBALS['DB_ERROR'] ?? 'database not connected';
    echo '<div style="color:#ffcc00;padding:8px 12px;">DB warning: ' . htmlspecialchars($db_msg) . '</div>';
} else {
    try {
        $stmt = $pdo->query("
            SELECT itemid, itemname, itemdesc, itemprice, itemthumb
            FROM dd_catalog
            WHERE COALESCE(active,1)=1
            ORDER BY itemid DESC
            LIMIT 12
        ");
        $featured = $stmt->fetchAll();
    } catch (Throwable $e) {
        echo '<div style="color:#ff8080;padding:8px 12px;">DB error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        $featured = [];
    }
}
?>

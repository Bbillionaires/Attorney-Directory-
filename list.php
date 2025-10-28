<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
require_once __DIR__ . '/includes.php';
@include __DIR__ . '/templates/HeaderTemplate.php';

$token = $_GET['token'] ?? '';
$need  = getenv('ADMIN_TOKEN') ?: '';

$rows = $pdo->query("SELECT itemid,itemname,itemprice,active FROM dd_catalog ORDER BY itemid DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Attorney Directory Listing</h2>

<?php if (!$rows): ?>
  <p class="muted">No items found. <?php if ($need!=='' && $token===$need): ?><a href="/add.php?token=<?= htmlspecialchars($token) ?>">Add one</a><?php endif; ?></p>
<?php else: ?>
  <ul>
    <?php foreach($rows as $r): ?>
      <li>
        <a href="/view_item.php?id=<?= $r['itemid'] ?>"><?= htmlspecialchars($r['itemname']) ?></a>
        — $<?= number_format((float)$r['itemprice'], 2) ?>
        <?php if ($need!=='' && $token===$need): ?>
          <span class="muted">[<?= (int)$r['active']===1?'active':'hidden' ?>]</span>
          · <a href="/edit.php?id=<?= $r['itemid'] ?>&token=<?= htmlspecialchars($token) ?>">edit</a>
          · <a href="/delete.php?id=<?= $r['itemid'] ?>&token=<?= htmlspecialchars($token) ?>">delete</a>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
  <?php if ($need!=='' && $token===$need): ?>
    <p><a class="btn" href="/add.php?token=<?= htmlspecialchars($token) ?>">+ Add new item</a></p>
  <?php endif; ?>
<?php endif; ?>

<?php @include __DIR__ . '/templates/FooterTemplate.php';

<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
require_once __DIR__ . '/includes.php';
@include __DIR__ . '/templates/HeaderTemplate.php';

$q = trim($_GET['q'] ?? '');
$limit = 25;
$page  = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$where = "WHERE COALESCE(active,1)=1";
$params = [];
if ($q !== '') {
  $where .= " AND (itemname ILIKE :q OR itemdesc ILIKE :q)";
  $params[':q'] = "%$q%";
}

$sql = "SELECT itemid,itemname,itemprice FROM dd_catalog
        $where
        ORDER BY itemid DESC
        LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
foreach ($params as $k=>$v) $stmt->bindValue($k, $v);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Attorney Directory Listing</h2>

<form class="searchbar" method="get" action="/list.php">
  <input type="text" name="q" placeholder="Search name or description…" value="<?= htmlspecialchars($q) ?>">
  <button class="btn" type="submit">Search</button>
</form>

<table class="table">
  <thead><tr><th>ID</th><th>Name</th><th class="price">Price</th><th></th></tr></thead>
  <tbody>
    <?php if (!$rows): ?>
      <tr><td colspan="4" class="muted">No items match your search.</td></tr>
    <?php else: foreach ($rows as $r): ?>
      <tr>
        <td>#<?= (int)$r['itemid'] ?></td>
        <td><?= htmlspecialchars($r['itemname']) ?></td>
        <td class="price">$<?= number_format((float)$r['itemprice'],2) ?></td>
        <td><a class="btn" href="/view_item.php?id=<?= (int)$r['itemid'] ?>">View</a></td>
      </tr>
    <?php endforeach; endif; ?>
  </tbody>
</table>

<p>
  <?php if ($page > 1): ?>
    <a class="btn secondary" href="/list.php?<?= http_build_query(['q'=>$q,'page'=>$page-1]) ?>">← Prev</a>
  <?php endif; ?>
  <a class="btn secondary" href="/list.php?<?= http_build_query(['q'=>$q,'page'=>$page+1]) ?>">Next →</a>
</p>

<?php @include __DIR__ . '/templates/FooterTemplate.php';

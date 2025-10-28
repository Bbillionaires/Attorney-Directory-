<?php
declare(strict_types=1);
require __DIR__ . '/conn.php';

$q = trim((string)($_GET['q'] ?? ''));
$sql = 'SELECT itemid, itemname, itemprice, itemthumb FROM dd_catalog WHERE COALESCE(active,1)=1';
$args = [];

if ($q !== '') {
  $sql .= ' AND (itemname ILIKE :q OR itemdesc ILIKE :q)';
  $args[':q'] = '%'.$q.'%';
}

$sql .= ' ORDER BY itemname ASC LIMIT 200';
$stmt = $pdo->prepare($sql);
$stmt->execute($args);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Attorney Directory — Public</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="/styles.css">
  <style>
    .wrap{max-width:960px;margin:24px auto;padding:0 16px}
    .top{display:flex;gap:8px;align-items:center;justify-content:space-between;flex-wrap:wrap}
    .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px;margin-top:16px}
    .card{border:1px solid #e5e7eb;border-radius:12px;padding:12px}
    .card img{width:100%;height:140px;object-fit:cover;border-radius:8px;background:#f3f4f6}
    .muted{color:#6b7280}
    .price{font-weight:600}
    .search{display:flex;gap:8px;width:100%;max-width:520px}
    input[type="search"]{flex:1;padding:10px 12px;border:1px solid #d1d5db;border-radius:10px}
    .btn{display:inline-block;padding:10px 12px;border-radius:10px;border:1px solid #111827;text-decoration:none}
    .btn.secondary{border-color:#d1d5db;color:#111827;background:#fff}
  </style>
</head>
<body>
  <div class="wrap">
    <header class="top">
      <h1>Attorney Directory</h1>
      <nav>
        <a class="btn secondary" href="/index.php">Admin Home</a>
        <a class="btn secondary" href="/add.php">Add New (Admin)</a>
      </nav>
    </header>

    <form class="search" method="get" action="/public_list.php">
      <input type="search" name="q" placeholder="Search attorneys or descriptions…" value="<?= htmlspecialchars($q) ?>">
      <button class="btn" type="submit">Search</button>
    </form>

    <?php if (!$rows): ?>
      <p class="muted" style="margin-top:16px">No public listings found.</p>
    <?php else: ?>
      <div class="grid">
        <?php foreach ($rows as $r): ?>
          <article class="card">
            <?php if (!empty($r['itemthumb'])): ?>
              <img src="<?= htmlspecialchars($r['itemthumb']) ?>" alt="">
            <?php endif; ?>
            <h3 style="margin:10px 0 6px"><?= htmlspecialchars($r['itemname']) ?></h3>
            <div class="muted price">$<?= number_format((float)$r['itemprice'], 2) ?></div>
            <p style="margin-top:10px">
              <a class="btn secondary" href="/view_item.php?id=<?= (int)$r['itemid'] ?>">View</a>
            </p>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <footer class="muted" style="margin-top:24px">© <?= date('Y') ?> Attorney Directory</footer>
  </div>
</body>
</html>

<?php
require __DIR__ . '/conn.php';          // $pdo
require __DIR__ . '/includes.php';
require_admin();      // shared header/nav/etc.

$rows = [];
$err  = '';
try {
  $stmt = $pdo->query("
    SELECT itemid, itemname, itemdesc,
           COALESCE(itemprice,0) AS itemprice,
           itemthumb, COALESCE(active,1) AS active
    FROM dd_catalog
    ORDER BY LOWER(itemname) ASC, itemid DESC
  ");
  $rows = $stmt->fetchAll();
} catch (Throwable $e) {
  $err = $e->getMessage();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>All Items · Attorney Directory</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/styles.css" />
</head>
<body class="bg-slate-950 text-slate-100">
<?php @include __DIR__ . '/templates/HeaderTemplate.php'; ?>

<main class="max-w-6xl mx-auto px-4 py-8">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">All Items</h1>
    <a href="/add" class="rounded-lg bg-sky-600 hover:bg-sky-700 text-white px-4 py-2">Add new</a>
  </div>

  <?php if ($err): ?>
    <div class="rounded-xl bg-red-900/40 border border-red-700 text-red-200 p-4 mb-6">
      DB error: <?= htmlspecialchars($err) ?>
    </div>
  <?php endif; ?>

  <?php if (!$rows): ?>
    <p class="text-slate-300">No items yet. <a class="text-sky-400 underline" href="/add">Add one</a>.</p>
  <?php else: ?>
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($rows as $r): ?>
        <article class="rounded-2xl overflow-hidden bg-slate-900/50 border border-slate-800">
          <?php if (!empty($r['itemthumb'])): ?>
            <img src="<?= htmlspecialchars($r['itemthumb']) ?>" alt="" class="w-full h-40 object-cover">
          <?php else: ?>
            <div class="w-full h-40 bg-slate-800 flex items-center justify-center text-slate-400">No image</div>
          <?php endif; ?>
          <div class="p-4 space-y-2">
            <h2 class="text-lg font-semibold line-clamp-2"><?= htmlspecialchars($r['itemname']) ?></h2>
            <p class="text-sm text-slate-300 line-clamp-3"><?= htmlspecialchars($r['itemdesc'] ?? '') ?></p>
            <div class="flex items-center justify-between pt-2">
              <span class="font-semibold">$<?= number_format((float)$r['itemprice'], 2) ?></span>
              <div class="flex gap-2">
                <a href="/view_item.php?itemid=<?= (int)$r['itemid'] ?>" class="rounded-md border border-slate-600 px-3 py-1.5 hover:bg-slate-800">View</a>
                <a href="/edit.php?itemid=<?= (int)$r['itemid'] ?>" class="rounded-md border border-slate-600 px-3 py-1.5 hover:bg-slate-800">Edit</a>
              </div>
            </div>
            <?php if (!(int)$r['active']): ?>
              <div class="text-xs text-amber-300 mt-1">Inactive</div>
            <?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>
</body>
</html>

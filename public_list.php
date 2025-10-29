<?php
declare(strict_types=1);
require __DIR__.'/conn.php';
$stmt = $pdo->query("SELECT itemid,itemname,itemprice,itemthumb FROM dd_catalog WHERE COALESCE(active,1)=1 ORDER BY itemid DESC");
$rows = $stmt->fetchAll();
?>
<?php include __DIR__.'/templates/HeaderTemplate.php'; ?>
<h2>Public Directory</h2>
<?php if (!$rows): ?>
  <p class="muted">No public items yet.</p>
<?php else: ?>
<section class="card p-4 mb-4">
  <div class="grid gap-3 md:grid-cols-3 items-center">
    <div class="field col-span-2"><svg width=18 height=18 viewBox="0 0 24 24" fill="none"><path d="M21 21l-4.3-4.3" stroke="#93c5fd" stroke-width="2"/><circle cx="11" cy="11" r="7" stroke="#93c5fd" stroke-width="2"/></svg><input id="dir-search" class="input" placeholder="Search agreements… (name/desc/price)"></div>
    <div class="chips justify-end">
      <select id="dir-price" class="field"><option value="">Any price</option><option value="0-0">Free</option><option value="0-50">Under $50</option><option value="50-200">$50 – $200</option><option value="200-100000">$200+</option></select>
      <label class="field"><input type="checkbox" id="dir-active" class="h-4 w-4"> <span>Active only</span></label>
    </div>
  </div>
</section>
  <ul>
    <?php foreach ($rows as $r): ?>
      <li>
        <a href="/view_item.php?id=<?= (int)$r['itemid'] ?>"><?= htmlspecialchars($r['itemname']) ?></a>
        — $<?= number_format((float)$r['itemprice'],2) ?>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
<?php include __DIR__.'/templates/FooterTemplate.php'; ?>

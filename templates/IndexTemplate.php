<?php
// Template displays the randomly selected item from index.php
?>
<section style="font-family:sans-serif;max-width:600px;margin:auto;">
  <h1>Attorney Directory</h1>
  <?php if (!empty($item)): ?>
    <article style="border:1px solid #ccc;padding:1em;margin-top:1em;border-radius:8px;">
      <h2><?=htmlspecialchars($item['itemname'])?></h2>
      <p><?=nl2br(htmlspecialchars($item['itemdesc'] ?? ''))?></p>
      <p><strong>Price:</strong> $<?=number_format((float)$item['itemprice'],2)?></p>
    </article>
  <?php else: ?>
    <p>No items found. Visit <a href="/list.php">/list.php</a> to view all entries.</p>
  <?php endif; ?>
</section>

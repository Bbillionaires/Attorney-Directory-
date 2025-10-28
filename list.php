<?php
// minimal read-only list to verify output in browser
$u = parse_url(getenv('DATABASE_URL') ?: getenv('PGURL'));
$host = $u['host']; $port = $u['port'] ?? 5432;
$db   = ltrim($u['path'], '/'); $user = $u['user']; $pass = $u['pass'];
$dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";
try {
  $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
  $sql = "SELECT itemid,itemname,itemdesc,itemprice,COALESCE(itemthumb,'') AS itemthumb
          FROM dd_catalog WHERE COALESCE(active,TRUE)=TRUE
          ORDER BY itemid LIMIT 50";
  $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
} catch(Throwable $e) {
  die("DB error: ".$e->getMessage());
}
?><!doctype html><meta name=viewport content="width=device-width, initial-scale=1">
<title>Items</title><h1>Items</h1>
<?php if (!$rows): ?>
<p>No items found.</p>
<?php else: ?>
<ul>
<?php foreach($rows as $r): ?>
  <li>
    <strong><?=htmlspecialchars($r['itemname'])?></strong>
    — $<?=number_format((float)$r['itemprice'],2)?>
  </li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

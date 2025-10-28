<?php
declare(strict_types=1);
require __DIR__ . '/conn.php';

function p($k,$d=null){ return $_POST[$k] ?? $d; }

$name  = trim((string) p('itemname',''));
$desc  = trim((string) p('itemdesc',''));
$price = (float) str_replace([',','$',' '], '', (string) p('itemprice','0'));
$thumb = trim((string) p('itemthumb',''));
$active = !empty($_POST['active']);
$id    = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;

if ($name === '') { http_response_code(422); exit('Name is required'); }

if ($id) {
  $stmt = $pdo->prepare('UPDATE dd_catalog
                          SET itemname=:n, itemdesc=:d, itemprice=:p, itemthumb=:t, active=:a
                          WHERE itemid=:id');
  $stmt->execute([':n'=>$name,':d'=>$desc,':p'=>$price,':t'=>$thumb,':a'=>$active,':id'=>$id]);
} else {
  $stmt = $pdo->prepare('INSERT INTO dd_catalog (itemname,itemdesc,itemprice,itemthumb,active)
                         VALUES (:n,:d,:p,:t,:a) RETURNING itemid');
  $stmt->execute([':n'=>$name,':d'=>$desc,':p'=>$price,':t'=>$thumb,':a'=>$active]);
  $id = (int)$stmt->fetchColumn();
}

header('Location: /view_item.php?id=' . $id);
exit;

<?php
declare(strict_types=1);
require __DIR__.'/conn.php';

function post(string $k, $default=null){ return $_POST[$k] ?? $default; }

$itemname  = trim((string)post('itemname',''));
$itemdesc  = trim((string)post('itemdesc',''));
$itemprice = (float)post('itemprice','0');
$itemthumb = trim((string)post('itemthumb',''));
$active    = !empty($_POST['active']) ? 1 : 0;

if ($itemname === '') {
  http_response_code(400);
  echo "Name is required."; exit;
}

$sql = "INSERT INTO dd_catalog (itemname,itemdesc,itemprice,itemthumb,active)
        VALUES (:name,:desc,:price,:thumb,:active)
        RETURNING itemid";
$stmt = $pdo->prepare($sql);
stmt->execute([
    'id'        => $itemid,
    'itemname'  => $_POST['itemname'] ?? '',
    'itemdesc'  => $_POST['itemdesc'] ?? '',
    'itemprice' => (float)($_POST['itemprice'] ?? 0),
    'itemthumb' => $_POST['itemthumb'] ?? '',
    'active'    => (empty($_POST['active']) ? 0 : 1),
]);
$itemid = (int)$stmt->fetchColumn();

header("Location: /view_item.php?id=".$itemid);
exit;

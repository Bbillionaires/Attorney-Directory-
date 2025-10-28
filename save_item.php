<?php
declare(strict_types=1);
require __DIR__ . '/conn.php';

$fields = [
  'itemid'    => $_POST['itemid']   ?? null,      // hidden when editing (optional)
  'itemname'  => trim($_POST['itemname'] ?? ''),
  'itemdesc'  => trim($_POST['itemdesc'] ?? ''),
  'itemprice' => (float)($_POST['itemprice'] ?? 0),
  'itemthumb' => trim($_POST['itemthumb'] ?? ''),
  'active'    => isset($_POST['active']) ? 1 : 0,  // force integer 1/0
];

if ($fields['itemid']) {
  $sql = "UPDATE dd_catalog
            SET itemname=:itemname, itemdesc=:itemdesc, itemprice=:itemprice,
                itemthumb=:itemthumb, active=:active
          WHERE itemid=:id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute($fields);
  $id = (int)$fields['itemid'];
} else {
  $sql = "INSERT INTO dd_catalog (itemname, itemdesc, itemprice, itemthumb, active)
          VALUES (:itemname, :itemdesc, :itemprice, :itemthumb, :active)
          RETURNING itemid";
  $stmt = $pdo->prepare($sql);
  $stmt->execute($fields);
  $id = (int)$stmt->fetchColumn();
}

header("Location: /view_item.php?id=".$id);
exit;

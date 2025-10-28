<?php
declare(strict_types=1);
require __DIR__ . '/conn.php';

// Normalize inputs
$itemid     = isset($_POST['itemid']) && $_POST['itemid'] !== '' ? (int)$_POST['itemid'] : null;
$itemname   = trim((string)($_POST['itemname'] ?? ''));
$itemdesc   = trim((string)($_POST['itemdesc'] ?? ''));
$itemthumb  = trim((string)($_POST['itemthumb'] ?? ''));
$active     = !empty($_POST['active']) ? 1 : 0;

// Price: allow "$199.00", "199", "199.5"
$rawPrice = (string)($_POST['itemprice'] ?? '0');
$rawPrice = preg_replace('/[^\d\.\-]/', '', $rawPrice) ?? '0';
$itemprice = (float)$rawPrice;

if ($itemname === '') {
  http_response_code(422);
  echo "Name is required.";
  exit;
}

if ($itemid === null) {
  // CREATE
  $sql = "INSERT INTO dd_catalog (itemname, itemdesc, itemprice, itemthumb, active)
          VALUES (:name, :desc, :price, :thumb, :active)
          RETURNING itemid";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':name'  => $itemname,
    ':desc'  => $itemdesc,
    ':price' => $itemprice,
    ':thumb' => $itemthumb,
    ':active'=> $active,
  ]);
  $newId = (int)$stmt->fetchColumn();
  header("Location: /view_item.php?id=".$newId);
  exit;
} else {
  // UPDATE
  $sql = "UPDATE dd_catalog
          SET itemname=:name, itemdesc=:desc, itemprice=:price, itemthumb=:thumb, active=:active
          WHERE itemid=:id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':name'  => $itemname,
    ':desc'  => $itemdesc,
    ':price' => $itemprice,
    ':thumb' => $itemthumb,
    ':active'=> $active,
    ':id'    => $itemid,
  ]);
  header("Location: /view_item.php?id=".$itemid);
  exit;
}

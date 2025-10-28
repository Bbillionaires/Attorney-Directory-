<?php
declare(strict_types=1);
require __DIR__ . '/conn.php';

$fields = [
  'name'  => trim($_POST['itemname'] ?? ''),
  'desc'  => trim($_POST['itemdesc'] ?? ''),
  'price' => (float)($_POST['itemprice'] ?? 0),
  'thumb' => trim($_POST['itemthumb'] ?? ''),
  'active'=> isset($_POST['active']) && $_POST['active'] ? 1 : 0,
];
$id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;

try {
  if ($id === null) {
    // INSERT: no :id here; DB assigns it. Return the new id.
    $sql = "INSERT INTO dd_catalog (itemname,itemdesc,itemprice,itemthumb,active)
            VALUES (:name,:desc,:price,:thumb,:active)
            RETURNING itemid";
    $stmt = $pdo->prepare($sql);
    // DEBUG
    // var_dump($sql, ["id"=>$itemid, "itemname"=>$itemname, "itemdesc"=>$itemdesc, "itemprice"=>$itemprice, "itemthumb"=>$itemthumb, "active"=>$active]);
    $stmt->execute($fields);
    $id = (int)$stmt->fetchColumn();
  } else {
    // UPDATE: uses WHERE itemid = :id and binds :id (not :id)
    $sql = "UPDATE dd_catalog
              SET itemname=:name,
                  itemdesc=:desc,
                  itemprice=:price,
                  itemthumb=:thumb,
                  active=:active
            WHERE itemid = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($fields + ['id' => $id]);
  }

  header("Location: /view_item.php?id={$id}");
  exit;
} catch (Throwable $e) {
  http_response_code(500);
  echo "Save failed: " . htmlspecialchars($e->getMessage());
}

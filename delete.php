<?php
require __DIR__ . '/includes.php';

$pdo = pdo();
$id  = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
  $stmt = $pdo->prepare('DELETE FROM dd_catalog WHERE itemid = :id');
  $stmt->execute([':id' => $id]);
}

header('Location: /list.php');
exit;

<?php
require __DIR__ . '/conn.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$pdo->prepare('UPDATE dd_catalog SET active = NOT active WHERE itemid=:id')->execute([':id'=>$id]);
header('Location: /view_item.php?id='.$id);

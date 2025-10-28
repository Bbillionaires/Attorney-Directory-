<?php
require __DIR__ . '/conn.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$pdo->prepare('DELETE FROM dd_catalog WHERE itemid=:id')->execute([':id'=>$id]);
header('Location: /list.php');

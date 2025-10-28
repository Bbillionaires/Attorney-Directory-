<?php
declare(strict_types=1);
require __DIR__.'/conn.php';
$id = (int)($_GET['id'] ?? 0);
$to = (int)($_GET['to'] ?? 0);
$pdo->prepare("UPDATE dd_catalog SET active=:a WHERE itemid=:i")->execute([':a'=>$to, ':i'=>$id]);
header("Location: /list.php");

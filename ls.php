<?php
header('Content-Type: text/plain');
$items = scandir(__DIR__);
foreach ($items as $i) echo $i,"\n";

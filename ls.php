<?php
header('Content-Type: text/plain');
foreach (scandir(__DIR__) as $f) { echo $f . "\n"; }

<?php
header('Content-Type: text/plain');
echo "PWD: " . getcwd() . "\n";
echo "PHP SAPI: " . php_sapi_name() . "\n";
echo "Index exists? " . (file_exists(__DIR__ . '/index.php') ? "yes" : "no") . "\n";
echo "\nFiles here:\n";
foreach (scandir(__DIR__) as $f) { echo " - $f\n"; }

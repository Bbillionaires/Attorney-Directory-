<?php
header('Content-Type: text/plain');
$build = @file_get_contents(__DIR__ . '/build-id.txt') ?: 'unknown';
echo "OK from which.php\nBUILD_ID: {$build}\n";

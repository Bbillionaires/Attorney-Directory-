<?php
header('Content-Type: text/plain');
$commit = @file_get_contents(__DIR__.'/COMMIT') ?: '(no COMMIT file)';
echo "DEPLOY_COMMIT: $commit\n";
echo "FILES IN /app:\n";
foreach (scandir(__DIR__) as $f) echo " - $f\n";

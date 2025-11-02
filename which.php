<?php
header('Content-Type: text/plain');
$build = @file_get_contents(__DIR__.'/build-id.txt') ?: 'dev';
echo "OK from which.php — BUILD_ID: $build — " . gmdate('c') . "\n";

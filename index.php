<?php
$bid = @file_get_contents(__DIR__.'/build-id.txt') ?: 'unknown';
echo "OK from index.php — BUILD_ID: {$bid} — " . date('c');

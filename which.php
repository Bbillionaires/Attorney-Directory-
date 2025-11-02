<?php
$build = @trim(@file_get_contents(__DIR__ . '/build-id.txt')) ?: 'unknown';
echo "OK from which.php — BUILD_ID: {$build} — " . gmdate('c');

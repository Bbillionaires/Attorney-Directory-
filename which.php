<?php header('Content-Type:text/plain');
$sha = trim(@shell_exec('git rev-parse --short HEAD'));
echo "WHICH OK\n";
echo "SHA=" . ($sha ?: 'n/a') . "\n";
echo "TIME=" . gmdate('c') . "\n";

<?php
echo "BRANCH=" . trim(shell_exec('git rev-parse --abbrev-ref HEAD')) . "\n";
echo "BUILD_ID=" . (is_file(__DIR__ . '/build-id.txt') ? trim(file_get_contents(__DIR__ . '/build-id.txt')) : 'none') . "\n";
echo "ROUTER=" . __FILE__ . "\n";
echo "INDEX_CALLS=";
echo (strpos(file_get_contents(__DIR__ . '/router.php'), "require __DIR__ . '/index.php';") !== false) ? 'index.php' : 'index_static.php';
echo "\n";

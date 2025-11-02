<?php
header('Content-Type: text/plain');
echo "whoami OK\n";
echo "BUILD_ID=" . (is_file(__DIR__ . '/build-id.txt') ? trim(file_get_contents(__DIR__ . '/build-id.txt')) : 'none') . "\n";
echo "time=" . gmdate('c') . "\n";
echo "cwd=" . getcwd() . "\n";

<?php header('Content-Type: text/plain');
echo "WHICH OK\n";
echo "BUILD_SHA=" . trim(`git rev-parse --short HEAD`) . "\n";
echo "BUILD_TIME=" . gmdate('c') . "\n";

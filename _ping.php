<?php
header('Content-Type: text/plain');
echo "OK\nPORT=".getenv('PORT')."\nBUILD_ID=".(@file_get_contents(__DIR__.'/build-id.txt')?:'unknown')."\n";
